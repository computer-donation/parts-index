<?php

namespace App\Command;

use App\Entity\Cpu;
use App\Enum\CpuVendor;
use App\Tests\Process\VoidProcess;
use App\Repository\CpuRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

use function Symfony\Component\String\u;

#[AsCommand(
    name: 'app:index-cpu',
    description: 'Lookup github repository for cpu, create if not exist.',
    hidden: false
)]
class IndexCpuCommand extends AbstractIndexCommand
{
    public function __construct(
        protected CpuRepository $cpuRepository,
        #[Autowire('%app.lscpu_dir%')]
        protected string $lscpuDir,
        #[Autowire('%app.lscpu_repo%')]
        protected string $lscpuRepo,
        #[Autowire(service: VoidProcess::class)]
        ?Process $process = null
    ) {
        parent::__construct($process);
    }

    protected function getDir(): string
    {
        return $this->lscpuDir;
    }

    protected function getRepo(): string
    {
        return $this->lscpuRepo;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateRepository($output);
        foreach (CpuVendor::cases() as $vendor) {
            $this->indexCpus($vendor, $output);
        }
        $output->writeln('Indexed all cpus!');

        return Command::SUCCESS;
    }

    protected function indexCpus(CpuVendor $vendor, OutputInterface $output): void
    {
        $output->writeln(sprintf('Indexing cpus for vendor %s...', $vendor->value));
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

    protected function indexCpu(SplFileInfo $file, CpuVendor $vendor, bool $flush): void
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
