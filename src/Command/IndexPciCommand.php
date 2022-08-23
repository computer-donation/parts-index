<?php

namespace App\Command;

use App\Entity\Computer;
use App\Entity\GraphicsCard;
use App\Enum\ComputerType;
use App\Repository\ComputerRepository;
use App\Repository\GraphicsCardRepository;
use App\Repository\ProbeRepository;
use App\Tests\Process\VoidProcess;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

use function Symfony\Component\String\u;

#[AsCommand(
    name: 'app:index-pci',
    description: 'Lookup github repository for pci devices, create if not exist.',
    hidden: false
)]
class IndexPciCommand extends AbstractIndexCommand
{
    public function __construct(
        ProbeRepository $probeRepository,
        protected ComputerRepository $computerRepository,
        protected GraphicsCardRepository $graphicsCardRepository,
        #[Autowire('%app.hwinfo_dir%')]
        protected string $hwinfoDir,
        #[Autowire('%app.hwinfo_repo%')]
        protected string $hwinfoRepo,
        #[Autowire(service: VoidProcess::class)]
        ?Process $process = null
    ) {
        parent::__construct($probeRepository, $process);
    }

    protected function getDir(): string
    {
        return $this->hwinfoDir;
    }

    protected function getRepo(): string
    {
        return $this->hwinfoRepo;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateRepository($output);
        foreach (ComputerType::cases() as $type) {
            $this->indexComputersAndPciDevices($type, $output);
        }
        $output->writeln('Indexed all pci devices!');

        return Command::SUCCESS;
    }

    protected function indexComputersAndPciDevices(ComputerType $type, OutputInterface $output): void
    {
        $output->writeln(sprintf('Indexing computers and pci devices for type %s...', $type->value));
        $finder = new Finder();
        $finder->files()->in($this->hwinfoDir.DIRECTORY_SEPARATOR.$type->value);
        $last = $finder->count();
        $current = 1;
        $progressBar = new ProgressBar($output, $last);
        foreach ($finder as $file) {
            $progressBar->advance();
            $flush = !($current % 100) || $current === $last;
            $this->indexComputer($file, $type, $flush, $output);
            $this->indexGraphicsCard($file, $flush);
            ++$current;
        }
        $progressBar->finish();
        $output->writeln(' Finished!');
    }

    protected function indexGraphicsCard(SplFileInfo $file, bool $flush): void
    {
        if (preg_match('/Hardware Class: graphics card.*?Vendor: pci 0x([^\s]+) "([^"]+)"\s+Device: pci 0x([^\s]+) "([^"]+)"\s+SubVendor: pci 0x([^\s]+) "([^"]+)"\s+SubDevice: pci 0x([^\s]+)/s', $file->getContents(), $matches)) {
            [, $vendorId, $vendor, $deviceId, $device, $subVendorId, $subVendor, $subDeviceId] = $matches;
            $id = u('-')->join([$vendorId, $deviceId, $subVendorId, $subDeviceId]);
            if (!$this->graphicsCardRepository->find($id)) {
                $graphicsCard = new GraphicsCard();
                $graphicsCard->id = $id;
                $graphicsCard->vendor = $vendor;
                $graphicsCard->device = $device;
                $graphicsCard->subVendor = $subVendor;
                $graphicsCard->addProbe($this->getProbe($file->getFilename()));
                $this->graphicsCardRepository->add($graphicsCard, $flush);
            }
        }
    }

    protected function indexComputer(SplFileInfo $file, ComputerType $type, bool $flush, OutputInterface $output): void
    {
        $items = explode(DIRECTORY_SEPARATOR, $file->getRelativePathname());
        if (8 !== count($items)) {
            $output->writeln(sprintf('<error>Invalid file path %s. It should follow this pattern %s</error>', $file->getPathname(), '{COMPUTER TYPE}/{VENDOR}/{MODEL PREFIX}/{MODEL}/{HWID}/{OS}/{KERNEL}/{ARCH}/{PROBE ID}'));

            return;
        }
        [$vendor, , $model, $hwid, , , , $probe] = $items;
        if (!$this->computerRepository->find($hwid)) {
            $computer = new Computer();
            $computer->id = $hwid;
            $computer->type = $type;
            $computer->vendor = $vendor;
            $computer->model = $model;
            $computer->addProbe($this->getProbe($probe));
            $this->computerRepository->add($computer, $flush);
        }
    }
}
