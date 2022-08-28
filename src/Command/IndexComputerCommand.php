<?php

namespace App\Command;

use App\Entity\Computer;
use App\Enum\ComputerType;
use App\Repository\ComputerRepository;
use App\Repository\ProbeRepository;
use App\Tests\Process\VoidProcess;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
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
        $this->updateRepository($this->sensorsRepo, $this->sensorsDir, $output);
        $this->updateRepository($this->hwinfoRepo, $this->hwinfoDir, $output);
        foreach (ComputerType::cases() as $type) {
            $this->indexComputers($type, $output);
        }
        $output->writeln('Indexed all computers!');

        return Command::SUCCESS;
    }

    protected function indexComputers(ComputerType $type, OutputInterface $output): void
    {
        $output->writeln(sprintf('Indexing computers for type %s...', $type->value));
        $this->browseFiles(
            [
                $this->sensorsDir.DIRECTORY_SEPARATOR.$type->value,
                $this->hwinfoDir.DIRECTORY_SEPARATOR.$type->value,
            ],
            $output,
            function (SplFileInfo $file, bool $flush) use ($type): void {
                $this->indexComputer($file, $type);
                $flush && $this->computerRepository->flush();
            }
        );
        $output->writeln(' Finished!');
    }

    protected function indexComputer(SplFileInfo $file, ComputerType $type): void
    {
        // {VENDOR}/{MODEL PREFIX}/{MODEL}/{HWID}/{OS}/{KERNEL}/{ARCH}/{PROBE ID}
        [$vendor, , $model, $hwid] = explode(DIRECTORY_SEPARATOR, $file->getRelativePathname());
        if (!$this->computerRepository->has($hwid)) {
            $computer = new Computer();
            $computer->id = $hwid;
            $computer->type = $type;
            $computer->vendor = $vendor;
            $computer->model = $model;
            $computer->addProbe($this->getProbe($file->getFilename()));
            $this->computerRepository->add($computer);
        }
    }
}