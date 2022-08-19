<?php

namespace App\Command;

use App\Entity\Computer;
use App\Entity\GraphicsCard;
use App\Enum\ComputerType;
use App\Repository\ComputerRepository;
use App\Repository\GraphicsCardRepository;
use App\Tests\Process\VoidProcess;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:index-pci',
    description: 'Lookup github repository for pci devices, create if not exist.',
    hidden: false
)]
class IndexPciCommand extends AbstractIndexCommand
{
    public function __construct(
        protected ComputerRepository $computerRepository,
        protected GraphicsCardRepository $graphicsCardRepository,
        #[Autowire('%app.hwinfo_dir%')]
        protected string $hwinfoDir,
        #[Autowire('%app.hwinfo_repo%')]
        protected string $hwinfoRepo,
        #[Autowire(service: VoidProcess::class)]
        ?Process $process = null
    ) {
        parent::__construct($process);
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
            $this->indexGraphicsCards($type, $output);
        }
        $output->writeln('Indexed all pci devices!');

        return Command::SUCCESS;
    }

    protected function indexGraphicsCards(ComputerType $type, OutputInterface $output): void
    {
        $output->writeln(sprintf('Indexing graphics cards for type %s...', $type->value));
        $finder = new Finder();
        $finder->files()->in($this->hwinfoDir.DIRECTORY_SEPARATOR.$type->value);
        $last = $finder->count();
        $current = 0;
        $progressBar = new ProgressBar($output, $last);
        foreach ($finder as $file) {
            ++$current;
            $progressBar->advance();
            $computer = $this->indexComputer($file, $type);
            $this->indexGraphicsCard($file, $computer, !($current % 100) || $current === $last);
        }
        $progressBar->finish();
        $output->writeln(' Finished!');
    }

    protected function indexGraphicsCard(SplFileInfo $file, Computer $computer, bool $flush): void
    {
        if (preg_match('/Unique ID: ([^\s]+)((?!VGA).)*Hardware Class: graphics card.*?Vendor: ([^\r|\n]+).*?Device: ([^\r|\n]+).*?SubVendor: ([^\r|\n]+)/s', $file->getContents(), $matches)) {
            [, $id, $vendor, $device, $subVendor] = $matches;
            if (!$graphicsCard = $this->graphicsCardRepository->find($id)) {
                $graphicsCard = new GraphicsCard();
                $graphicsCard->id = $id;
                $graphicsCard->vendor = $vendor;
                $graphicsCard->device = $device;
                $graphicsCard->subVendor = $subVendor;
                $graphicsCard->computer = $computer;
                $this->graphicsCardRepository->add($graphicsCard, $flush);
            }
        }
    }

    protected function indexComputer(SplFileInfo $file, ComputerType $type): Computer
    {
        [, $vendor, $model, $hwid, , , , $probe] = explode(DIRECTORY_SEPARATOR, $file->getRelativePathname());
        if (!$computer = $this->computerRepository->find($hwid)) {
            $computer = new Computer();
            $computer->id = $hwid;
            $computer->type = $type;
            $computer->vendor = $vendor;
            $computer->model = $model;
            $computer->probe = $probe;
            $this->computerRepository->add($computer, true);
        }

        return $computer;
    }
}
