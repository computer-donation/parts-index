<?php

namespace App\Command;

use App\Entity\Cpu;
use App\Enum\Cpu\Vendor;
use App\Repository\CpuRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use function Symfony\Component\String\u;

#[AsCommand(
    name: 'app:index-cpu',
    description: 'Lookup github repository for cpu, create if not exist.',
    hidden: false
)]
class IndexCpuCommand extends Command
{
    public function __construct(
        protected CpuRepository $cpuRepository,
        protected string $lscpuDir,
        protected string $lscpuRepo,
        protected ?Process $process = null
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateRepository($output);
        foreach (Vendor::cases() as $vendor) {
            $output->writeln(sprintf('Indexing cpus for vendor %s...', $vendor->value));
            $this->indexCpus($vendor, $output);
        }
        $output->writeln('Indexed all cpus!');

        return Command::SUCCESS;
    }

    protected function updateRepository(OutputInterface $output): void
    {
        $output->writeln('Updating cpu repository...');
        if (!is_dir($this->lscpuDir)) {
            $this->runProcess(['git', 'clone', $this->lscpuRepo, $this->lscpuDir]);
        } else {
            $this->runProcess(['git', '-C', $this->lscpuDir, 'pull']);
        }
    }

    protected function runProcess(array $command): void
    {
        $process = $this->process ?? new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    protected function indexCpus(Vendor $vendor, OutputInterface $output): void
    {
        $finder = new Finder();
        $finder->files()->in($this->lscpuDir.DIRECTORY_SEPARATOR.$vendor->value);
        $last = $finder->count();
        $current = 0;
        $progressBar = new ProgressBar($output, $last);
        foreach ($finder as $file) {
            ++$current;
            $progressBar->advance();
            $this->indexCpu($file, $vendor, !($current % 100) || $current === $last);
        }
        $progressBar->finish();
        $output->writeln(' Finished!');
    }

    protected function indexCpu(SplFileInfo $file, Vendor $vendor, bool $flush): void
    {
        [, $model, $code, $probe] = explode(DIRECTORY_SEPARATOR, $file->getRelativePathname());
        $id = u('-')->join([$vendor->value, $code, $model])->lower()->replace(' ', '-');
        if (!$this->cpuRepository->find($id)) {
            $cpu = new Cpu();
            $cpu->id = $id;
            $cpu->vendor = $vendor;
            $cpu->model = $model;
            $cpu->probe = $probe;
            $this->cpuRepository->add($cpu, $flush);
        }
    }
}
