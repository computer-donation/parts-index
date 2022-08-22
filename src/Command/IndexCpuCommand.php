<?php

namespace App\Command;

use App\Entity\Cpu;
use App\Enum\CpuVendor;
use App\Repository\CpuRepository;
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

use function Symfony\Component\String\u;

#[AsCommand(
    name: 'app:index-cpu',
    description: 'Lookup github repository for cpu, create if not exist.',
    hidden: false
)]
class IndexCpuCommand extends AbstractIndexCommand
{
    public function __construct(
        ProbeRepository $probeRepository,
        protected CpuRepository $cpuRepository,
        #[Autowire('%app.lscpu_dir%')]
        protected string $lscpuDir,
        #[Autowire('%app.lscpu_repo%')]
        protected string $lscpuRepo,
        Connection $connection,
        #[Autowire(service: VoidProcess::class)]
        ?Process $process = null
    ) {
        parent::__construct($probeRepository, $connection, $process);
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
        $this->disableLogging();
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
        $current = 1;
        $progressBar = new ProgressBar($output, $last);
        foreach ($finder as $file) {
            $progressBar->advance();
            $flush = !($current % 100) || $current === $last;
            $this->indexCpu($file, $vendor, $flush, $output);
            ++$current;
        }
        $progressBar->finish();
        $output->writeln(' Finished!');
    }

    protected function indexCpu(SplFileInfo $file, CpuVendor $vendor, bool $flush, OutputInterface $output): void
    {
        $items = explode(DIRECTORY_SEPARATOR, $file->getRelativePathname());
        if (4 !== count($items)) {
            $output->writeln(sprintf('<error>Invalid file path %s. It should follow this pattern %s</error>', $file->getPathname(), '{VENDOR}/{MODEL PREFIX}/{MODEL NAME}/{FAMILY}-{MODEL}-{STEPPING}/{PROBE ID}'));

            return;
        }
        [, $model, $code, $probe] = $items;
        $id = u('-')->join([$vendor->value, $code, $model])->lower()->replace(' ', '-');
        if (0 === $this->cpuRepository->count(['id' => $id])) {
            $cpu = new Cpu();
            $cpu->id = $id;
            $cpu->vendor = $vendor;
            $cpu->model = $model;
            $cpu->addProbe($this->getProbe($probe));
            $this->cpuRepository->add($cpu, $flush);
        }
    }
}
