<?php

namespace App\Command;

use App\Entity\EthernetPciCard;
use App\Entity\GraphicsCard;
use App\Entity\Printer;
use App\Enum\ComputerType;
use App\Repository\EthernetPciCardRepository;
use App\Repository\GraphicsCardRepository;
use App\Repository\PrinterRepository;
use App\Service\CsvExport;
use App\Tests\Process\VoidProcess;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

use function Symfony\Component\String\u;

#[AsCommand(
    name: 'app:index-pci',
    description: 'Lookup github repository for pci devices, create if not exist.',
    hidden: false
)]
class IndexPciCommand extends Command
{
    use RepoTrait;
    use FileTrait;
    use CsvTrait;

    public const GPU_CSV_HEADER = ['gpuId', 'vendor', 'subVendor', 'device', 'probeId'];
    public const PRINTER_CSV_HEADER = ['printerId', 'vendor', 'device'];
    public const ETHERNET_CSV_HEADER = ['ethernetId', 'vendor', 'subVendor', 'device', 'probeId'];

    public const GPU_CSV_FILE_NAME = 'gpu.csv';
    public const PRINTER_CSV_FILE_NAME = 'printer.csv';
    public const ETHERNET_CSV_FILE_NAME = 'ethernet.csv';

    public function __construct(
        protected GraphicsCardRepository $graphicsCardRepository,
        protected PrinterRepository $printerRepository,
        protected EthernetPciCardRepository $ethernetPciCardRepository,
        protected CsvExport $csvExport,
        #[Autowire('%app.hwinfo_dir%')]
        protected string $hwinfoDir,
        #[Autowire('%app.hwinfo_repo%')]
        protected string $hwinfoRepo,
        #[Autowire(service: VoidProcess::class)]
        protected ?Process $process = null
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateRepository($this->hwinfoRepo, $this->hwinfoDir, $output);
        $this->checkCsv($this->csvExport->getCsvPath(static::GPU_CSV_FILE_NAME), static::GPU_CSV_HEADER, $input, $output);
        $this->checkCsv($this->csvExport->getCsvPath(static::PRINTER_CSV_FILE_NAME), static::PRINTER_CSV_HEADER, $input, $output);
        $this->checkCsv($this->csvExport->getCsvPath(static::ETHERNET_CSV_FILE_NAME), static::ETHERNET_CSV_HEADER, $input, $output);
        foreach (ComputerType::cases() as $type) {
            $this->indexPciDevices($type, $output);
        }
        $output->writeln('Indexed all pci devices!');

        return Command::SUCCESS;
    }

    protected function indexPciDevices(ComputerType $type, OutputInterface $output): void
    {
        $output->writeln(sprintf('Indexing pci devices for type %s...', $type->value));
        $this->browseFiles(
            $this->hwinfoDir.DIRECTORY_SEPARATOR.$type->value,
            $output,
            function (SplFileInfo $file, bool $flush): void {
                $this->indexGraphicsCard($file);
                $this->indexPrinters($file);
                $this->indexEthernetPciCard($file);
                if ($flush) {
                    $this->printerRepository->flush(); // Doesn't matter which repository flush the changes
                    $this->csvExport->flush(static::GPU_CSV_FILE_NAME);
                    $this->csvExport->flush(static::PRINTER_CSV_FILE_NAME);
                    $this->csvExport->flush(static::ETHERNET_CSV_FILE_NAME);
                }
            }
        );
        $output->writeln(' Finished!');
    }

    protected function indexGraphicsCard(SplFileInfo $file): void
    {
        if ($count = preg_match_all('/Hardware Class: graphics card.*?Vendor: pci 0x([^\s]+) "([^"]+)"\s+Device: pci 0x([^\s]+) "([^"]+)"\s+(SubVendor: pci 0x([^\s]+) "([^"]+)"\s+SubDevice: pci 0x([^\s]+))?/s', $file->getContents(), $matches)) {
            for ($column = 0; $column < $count; ++$column) {
                @[, $vendorId, $vendor, $deviceId, $device, , $subVendorId, $subVendor, $subDeviceId] = array_column($matches, $column);
                $id = u('-')->join([$vendorId, $deviceId, $subVendorId, $subDeviceId])->trim('-');
                if (!$this->graphicsCardRepository->has($id)) {
                    $graphicsCard = new GraphicsCard();
                    $graphicsCard->id = $id;
                    $graphicsCard->vendor = $vendor;
                    $graphicsCard->device = $device;
                    $graphicsCard->subVendor = $subVendor;
                    $this->graphicsCardRepository->add($graphicsCard);
                    $this->csvExport->addRow(static::GPU_CSV_FILE_NAME, [$id, $vendor, $subVendor, $device, $file->getFilename()]);
                }
            }
        }
    }

    protected function indexPrinters(SplFileInfo $file): void
    {
        if ($count = preg_match_all('/Hardware Class: printer.*?Vendor: usb 0x([^\s]+) "([^"]+)"\s+Device: usb 0x([^\s]+) "([^"]+)"/s', $file->getContents(), $matches)) {
            for ($column = 0; $column < $count; ++$column) {
                [, $vendorId, $vendor, $deviceId, $device] = array_column($matches, $column);
                $id = "usb:$vendorId-$deviceId";
                if (!$this->printerRepository->has($id)) {
                    $printer = new Printer();
                    $printer->id = $id;
                    $printer->vendor = $vendor;
                    $printer->device = $device;
                    $this->printerRepository->add($printer);
                    $this->csvExport->addRow(static::PRINTER_CSV_FILE_NAME, [$id, $vendor, $device]);
                }
            }
        }
    }

    protected function indexEthernetPciCard(SplFileInfo $file): void
    {
        if ($count = preg_match_all('/0200 Ethernet controller.*?Hardware Class: network.*?Vendor: pci 0x([^\s]+) "([^"]+)"\s+Device: pci 0x([^\s]+) "([^"]+)"\s+SubVendor: pci 0x([^\s]+) "([^"]+)"\s+SubDevice: pci 0x([^\s]+)/s', $file->getContents(), $matches)) {
            for ($column = 0; $column < $count; ++$column) {
                @[, $vendorId, $vendor, $deviceId, $device, $subVendorId, $subVendor, $subDeviceId] = array_column($matches, $column);
                $id = u('-')->join([$vendorId, $deviceId, $subVendorId, $subDeviceId]);
                if (!$this->ethernetPciCardRepository->has($id)) {
                    $ethernetPciCard = new EthernetPciCard();
                    $ethernetPciCard->id = $id;
                    $ethernetPciCard->vendor = $vendor;
                    $ethernetPciCard->device = $device;
                    $ethernetPciCard->subVendor = $subVendor;
                    $this->ethernetPciCardRepository->add($ethernetPciCard);
                    $this->csvExport->addRow(static::ETHERNET_CSV_FILE_NAME, [$id, $vendor, $subVendor, $device, $file->getFilename()]);
                }
            }
        }
    }
}
