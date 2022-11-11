<?php

namespace App\Command;

use App\Entity\Cpu;
use App\Enum\CpuVendor;
use App\Repository\CpuRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\String\Slugger\SluggerInterface;

use function Symfony\Component\String\u;

#[AsCommand(
    name: 'app:index-cpu',
    description: 'Lookup github repository for cpu, create if not exist.',
    hidden: false
)]
class IndexCpuCommand extends Command
{
    use RepoTrait;
    use FileTrait;
    use CsvTrait;

    public const REPO = 'LsCPU';
    public const CPU_CSV_HEADER = ['cpuId', 'vendor', 'model', 'probeId'];
    public const CSV_FILE_NAME = 'cpu.csv';

    public function __construct(
        protected SluggerInterface $slugger,
        protected CpuRepository $cpuRepository,
        #[Autowire('%app.lscpu_repo%')]
        protected string $lscpuRepo
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateRepository($this->lscpuRepo, $this->getRepoDir(static::REPO), $output);
        $this->checkCsv($this->csvExport->getCsvPath(static::CSV_FILE_NAME), static::CPU_CSV_HEADER, $input, $output);
        foreach (CpuVendor::cases() as $vendor) {
            $this->indexCpus($vendor, $output);
        }
        $output->writeln('Indexed all cpus!');

        return Command::SUCCESS;
    }

    protected function indexCpus(CpuVendor $vendor, OutputInterface $output): void
    {
        $output->writeln(sprintf('Indexing cpus for vendor %s...', $vendor->value));
        $this->browseFiles(
            $this->getRepoDir(static::REPO).DIRECTORY_SEPARATOR.$vendor->value,
            $output,
            function (SplFileInfo $file, bool $flush) use ($vendor, $output): void {
                $this->indexCpu($file, $vendor, $output);
                if ($flush) {
                    $this->cpuRepository->flush();
                    $this->csvExport->flush(static::CSV_FILE_NAME);
                }
            }
        );
        $output->writeln(' Finished!');
    }

    protected function indexCpu(SplFileInfo $file, CpuVendor $vendor, OutputInterface $output): void
    {
        if (preg_match('/Model name:\s+([^\r\n]+)/s', $file->getContents(), $matches)) {
            $model = $matches[1];
        } else {
            $output->writeln(sprintf('<error>Missing model name in %s</error>', $vendor->value.DIRECTORY_SEPARATOR.$file->getRelativePathname()));

            return;
        }
        $id = $this->slugger->slug(u($model)->lower()->replace('(r)', ' ')->replace('(tm)', ' ')->ensureStart($vendor->lower().' '));
        if (!$this->cpuRepository->has($id)) {
            $cpu = new Cpu();
            $cpu->id = $id;
            $cpu->vendor = $vendor;
            $cpu->model = $model;
            if (preg_match('/Core\(s\) per socket:\s+(\d+)/s', $file->getContents(), $matches)) {
                $cpu->cores = $matches[1];
            }
            if (preg_match('/CPU\(s\):\s+(\d+)/s', $file->getContents(), $matches)) {
                $cpu->threads = $matches[1];
            }
            if (preg_match('/CPU max MHz:\s+(\d+)(\.|\,)\d+/s', $file->getContents(), $matches)) {
                $cpu->maxSpeed = $matches[1];
            }
            if (preg_match('/CPU min MHz:\s+(\d+)(\.|\,)\d+/s', $file->getContents(), $matches)) {
                $cpu->minSpeed = $matches[1];
            }
            if (preg_match('/L2 cache:\s+([^\r\n]+)/s', $file->getContents(), $matches)) {
                $cpu->l2Cache = $matches[1];
            }
            if (preg_match('/L3 cache:\s+([^\r\n]+)/s', $file->getContents(), $matches)) {
                $cpu->l3Cache = $matches[1];
            }
            $this->cpuRepository->add($cpu);
            $this->csvExport->addRow(static::CSV_FILE_NAME, [$id, $vendor->value, $model, $file->getFilename()]);
        }
    }
}
