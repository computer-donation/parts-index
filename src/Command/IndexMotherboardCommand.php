<?php

namespace App\Command;

use App\Entity\Motherboard;
use App\Enum\ComputerType;
use App\Repository\ComputerRepository;
use App\Repository\MotherboardRepository;
use App\Tests\Process\VoidProcess;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

use function Symfony\Component\String\u;

#[AsCommand(
    name: 'app:index-motherboard',
    description: 'Lookup github repository for motherboards, create if not exist.',
    hidden: false
)]
class IndexMotherboardCommand extends Command
{
    use RepoTrait;
    use FileTrait;

    public function __construct(
        protected ComputerRepository $computerRepository,
        protected MotherboardRepository $motherboardRepository,
        #[Autowire('%app.dmi_dir%')]
        protected string $dmiDir,
        #[Autowire('%app.dmi_repo%')]
        protected string $dmiRepo,
        #[Autowire(service: VoidProcess::class)]
        protected ?Process $process = null
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->computerRepository->count([])) {
            throw new RuntimeException('Need to run command app:index-computer first!');
        }
        $this->updateRepository($this->dmiRepo, $this->dmiDir, $output);
        foreach (ComputerType::cases() as $type) {
            $this->indexMotherboards($type, $output);
        }
        $output->writeln('Indexed all motherboards!');

        return Command::SUCCESS;
    }

    protected function indexMotherboards(ComputerType $type, OutputInterface $output): void
    {
        $output->writeln(sprintf('Indexing motherboards for type %s...', $type->value));
        $this->browseFiles(
            $this->dmiDir.DIRECTORY_SEPARATOR.$type->value,
            $output,
            function (SplFileInfo $file, bool $flush) use ($output): void {
                $this->indexMotherboard($file, $output);
                $flush && $this->motherboardRepository->flush();
            }
        );
        $output->writeln(' Finished!');
    }

    protected function indexMotherboard(SplFileInfo $file, OutputInterface $output): void
    {
        if (!$this->computerRepository->has($file->getFilename())) {
            $output->writeln(sprintf('<error>Computer %s not found</error>', $file->getFilename()));

            return;
        }
        if (preg_match('/Base Board Information\s+Manufacturer: (.+)\s+Product Name: (.+)\s+Version: (.+)/', $file->getContents(), $matches)) {
            [, $manufacturer, $productName, $version] = $matches;
            $id = u('-')->join([$manufacturer, $productName, trim($version)])->lower()->replace(' ', '-')->trim('-');
            if (!$this->motherboardRepository->has($id)) {
                $motherboard = new Motherboard();
                $motherboard->id = $id;
                $motherboard->manufacturer = $manufacturer;
                $motherboard->productName = $productName;
                $motherboard->version = trim($version);
                $motherboard->addComputer($this->computerRepository->reference($file->getFilename()));
                $this->motherboardRepository->add($motherboard);
            }
        }
    }
}
