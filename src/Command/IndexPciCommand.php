<?php

namespace App\Command;

use App\Entity\Computer;
use App\Entity\GraphicsCard;
use App\Enum\ComputerType;
use App\Repository\ComputerRepository;
use App\Repository\GraphicsCardRepository;
use App\Repository\ProbeRepository;
use App\Tests\Process\VoidProcess;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

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
        Connection $connection,
        #[Autowire(service: VoidProcess::class)]
        ?Process $process = null
    ) {
        parent::__construct($probeRepository, $connection, $process);
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
        $this->disableLogging();
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
        $current = 0;
        $progressBar = new ProgressBar($output, $last);
        foreach ($finder as $file) {
            ++$current;
            $progressBar->advance();
            $flush = !($current % 100) || $current === $last;
            $this->indexComputer($file, $type, $flush);
            $this->indexGraphicsCard($file, $flush);
        }
        $progressBar->finish();
        $output->writeln(' Finished!');
    }

    protected function indexGraphicsCard(SplFileInfo $file, bool $flush): void
    {
        if (preg_match('/Unique ID: ([^\s]+)((?!VGA).)*Hardware Class: graphics card.*?Vendor: ([^\r|\n]+).*?Device: ([^\r|\n]+).*?SubVendor: ([^\r|\n]+)/s', $file->getContents(), $matches)) {
            [, $id, $vendor, $device, $subVendor] = $matches;
            $id = str_replace('.', '-', $id);
            if (!$graphicsCard = $this->graphicsCardRepository->find($id)) {
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

    protected function indexComputer(SplFileInfo $file, ComputerType $type, bool $flush): Computer
    {
        [, $vendor, $model, $hwid, , , , $probe] = explode(DIRECTORY_SEPARATOR, $file->getRelativePathname());
        if (!$computer = $this->computerRepository->find($hwid)) {
            $computer = new Computer();
            $computer->id = $hwid;
            $computer->type = $type;
            $computer->vendor = $vendor;
            $computer->model = $model;
            $computer->addProbe($this->getProbe($probe));
            $this->computerRepository->add($computer, $flush);
        }

        return $computer;
    }
}
