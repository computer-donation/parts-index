<?php

namespace App\Command;

use App\Entity\Motherboard;
use App\Enum\ComputerType;
use App\Graph\Node\MotherboardRepository as MotherboardNodeRepository;
use App\Graph\Relationship\ComputerMotherboardRepository;
use App\Repository\MotherboardRepository;
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
    name: 'app:index-motherboard',
    description: 'Lookup github repository for motherboards, create if not exist.',
    hidden: false
)]
class IndexMotherboardCommand extends Command
{
    use RepoTrait;
    use FileTrait;

    public function __construct(
        protected SluggerInterface $slugger,
        protected MotherboardRepository $motherboardRepository,
        protected MotherboardNodeRepository $motherboardNodeRepository,
        protected ComputerMotherboardRepository $computerMotherboardRelationshipRepository,
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
        $this->updateRepository($this->dmiRepo, $this->dmiDir, $output);
        $this->motherboardNodeRepository->setUp();
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
        if (preg_match('/Base Board Information\s+Manufacturer: (.+)\s+Product Name: (.+)\s+Version: (.+)/', $file->getContents(), $matches)) {
            [, $manufacturer, $productName, $version] = $matches;
            $id = $this->slugger->slug(u('-')->join([$manufacturer, $productName, $version])->lower());
            if (!$this->motherboardRepository->has($id)) {
                $motherboard = new Motherboard();
                $motherboard->id = $id;
                $motherboard->manufacturer = $manufacturer;
                $motherboard->productName = $productName;
                $motherboard->version = trim($version);
                $this->motherboardRepository->add($motherboard);
            }
            $this->motherboardNodeRepository->create($id, $manufacturer, $productName, trim($version));
            $this->computerMotherboardRelationshipRepository->create($file->getFilename(), $id);
        }
    }
}
