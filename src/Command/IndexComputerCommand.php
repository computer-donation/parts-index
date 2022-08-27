<?php

namespace App\Command;

use App\Entity\Computer;
use App\Enum\ComputerType;
use App\Repository\ComputerRepository;
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

#[AsCommand(
    name: 'app:index-computer',
    description: 'Lookup github repository for computer, create if not exist.',
    hidden: false
)]
class IndexComputerCommand extends Command
{
    use RepoTrait;
    use ProbeTrait;
    use FileTrait;

    public function __construct(
        protected ProbeRepository $probeRepository,
        protected ComputerRepository $computerRepository,
        #[Autowire('%app.sensors_dir%')]
        protected string $sensorsDir,
        #[Autowire('%app.sensors_repo%')]
        protected string $sensorsRepo,
        #[Autowire(service: VoidProcess::class)]
        protected ?Process $process = null
    ) {
        parent::__construct();
    }

    protected function getDir(): string
    {
        return $this->sensorsDir;
    }

    protected function getRepo(): string
    {
        return $this->sensorsRepo;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateRepository($output);
        foreach (ComputerType::cases() as $type) {
            $this->indexComputers($type, $output);
        }
        $output->writeln('Indexed all computers!');

        return Command::SUCCESS;
    }

    protected function indexComputers(ComputerType $type, OutputInterface $output): void
    {
        $output->writeln(sprintf('Indexing computers for type %s...', $type->value));
        $finder = new Finder();
        $finder->files()->in($this->getDir().DIRECTORY_SEPARATOR.$type->value);
        $last = $finder->count();
        $current = 1;
        $progressBar = new ProgressBar($output, $last);
        foreach ($finder as $file) {
            $progressBar->advance();
            $flush = !($current % 100) || $current === $last;
            $this->indexComputer($file, $type, $flush, $output);
            ++$current;
        }
        $progressBar->finish();
        $output->writeln(' Finished!');
    }

    protected function indexComputer(SplFileInfo $file, ComputerType $type, bool $flush, OutputInterface $output): void
    {
        $dirs = explode(DIRECTORY_SEPARATOR, $file->getRelativePathname());
        if (!$dirs = $this->getDirs($file, $output, '{COMPUTER TYPE}/{VENDOR}/{MODEL PREFIX}/{MODEL}/{HWID}/{OS}/{KERNEL}/{ARCH}/{PROBE ID}')) {
            return;
        }
        [$vendor, , $model, $hwid, , , , $probe] = $dirs;
        if (!$this->computerRepository->has($hwid)) {
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
