<?php

namespace App\Command;

use App\Entity\Motherboard;
use App\Enum\ComputerType;
use App\Repository\MotherboardRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\SplFileInfo;
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
    use CsvTrait;

    public const MOTHERBOARD_CSV_HEADER = ['motherboardId', 'manufacturer', 'productName', 'version', 'computerId'];
    public const CSV_FILE_NAME = 'motherboard.csv';

    public function __construct(
        protected SluggerInterface $slugger,
        protected MotherboardRepository $motherboardRepository,
        #[Autowire('%app.dmi_dir%')]
        protected string $dmiDir,
        #[Autowire('%app.dmi_repo%')]
        protected string $dmiRepo
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateRepository($this->dmiRepo, $this->dmiDir, $output);
        $this->checkCsv($this->csvExport->getCsvPath(static::CSV_FILE_NAME), static::MOTHERBOARD_CSV_HEADER, $input, $output);
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
                if ($flush) {
                    $this->motherboardRepository->flush();
                    $this->csvExport->flush(static::CSV_FILE_NAME);
                }
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
                $this->csvExport->addRow(static::CSV_FILE_NAME, [$id, $manufacturer, $productName, trim($version), $file->getFilename()]);
            }
        }
    }
}
