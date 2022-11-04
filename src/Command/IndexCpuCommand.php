<?php

namespace App\Command;

use App\Entity\Cpu;
use App\Enum\CpuVendor;
use App\Neo4j\Node\CpuRepository as CpuNodeRepository;
use App\Neo4j\Relationship\ProbeCpuRepository;
use App\Repository\CpuRepository;
use App\Tests\Process\VoidProcess;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
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

    public function __construct(
        protected SluggerInterface $slugger,
        protected CpuRepository $cpuRepository,
        protected CpuNodeRepository $cpuNodeRepository,
        protected ProbeCpuRepository $probeCpuRelationshipRepository,
        #[Autowire('%app.lscpu_dir%')]
        protected string $lscpuDir,
        #[Autowire('%app.lscpu_repo%')]
        protected string $lscpuRepo,
        #[Autowire(service: VoidProcess::class)]
        protected ?Process $process = null
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateRepository($this->lscpuRepo, $this->lscpuDir, $output);
        $this->cpuNodeRepository->setUp();
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
            $this->lscpuDir.DIRECTORY_SEPARATOR.$vendor->value,
            $output,
            function (SplFileInfo $file, bool $flush) use ($vendor, $output): void {
                $this->indexCpu($file, $vendor, $output);
                if ($flush) {
                    $this->cpuRepository->flush();
                    $this->cpuNodeRepository->flush(); // Doesn't matter which repository flush the changes
                }
            }
        );
        $output->writeln(' Finished!');
    }

    protected function indexCpu(SplFileInfo $file, CpuVendor $vendor, OutputInterface $output): void
    {
        if (preg_match('/Core\(s\) per socket:\s+(\d+)/s', $file->getContents(), $matches)) {
            $cores = $matches[1];
        }
        if (preg_match('/CPU\(s\):\s+(\d+)/s', $file->getContents(), $matches)) {
            $threads = $matches[1];
        }
        if (preg_match('/CPU max MHz:\s+(\d+)(\.|\,)\d+/s', $file->getContents(), $matches)) {
            $maxSpeed = $matches[1];
        }
        if (preg_match('/CPU min MHz:\s+(\d+)(\.|\,)\d+/s', $file->getContents(), $matches)) {
            $minSpeed = $matches[1];
        }
        if (preg_match('/L2 cache:\s+([^\r\n]+)/s', $file->getContents(), $matches)) {
            $l2Cache = $matches[1];
        }
        if (preg_match('/L3 cache:\s+([^\r\n]+)/s', $file->getContents(), $matches)) {
            $l3Cache = $matches[1];
        }
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
            $cpu->cores = $cores ?? null;
            $cpu->threads = $threads ?? null;
            $cpu->maxSpeed = $maxSpeed ?? null;
            $cpu->minSpeed = $minSpeed ?? null;
            $cpu->l2Cache = $l2Cache ?? null;
            $cpu->l3Cache = $l3Cache ?? null;
            $this->cpuRepository->add($cpu);
        }
        $this->cpuNodeRepository->create($id, $vendor->value, $model);
        $this->probeCpuRelationshipRepository->create($file->getFilename(), $id);
    }
}
