<?php

namespace App\Command;

use App\Entity\Computer;
use App\Entity\Part;
use App\Enum\ComputerType;
use App\Repository\ComputerRepository;
use App\Repository\PartRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\SplFileInfo;

#[AsCommand(
    name: 'app:index',
    description: 'Index computers and parts to database.',
    hidden: false
)]
class IndexCommand extends Command
{
    use RepoTrait;
    use FileTrait;
    use CsvTrait;

    public const HWINFO_REPO = 'HWInfo';
    public const COMPUTER_CSV_HEADER = ['computerId', 'type', 'vendor', 'model'];
    public const PART_CSV_HEADER = ['partId', 'type', 'model'];
    public const HAS_PART_CSV_HEADER = ['computerId', 'partId'];
    public const COMPUTER_CSV_FILE_NAME = 'computer.csv';
    public const PART_CSV_FILE_NAME = 'part.csv';
    public const HAS_PART_CSV_FILE_NAME = 'HAS_PART.csv';

    public function __construct(
        protected ComputerRepository $computerRepository,
        protected PartRepository $partRepository,
        #[Autowire('%app.hwinfo_repo%')]
        protected string $hwinfoRepo
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateRepository($this->hwinfoRepo, $this->getRepoDir(static::HWINFO_REPO), $output);
        $this->checkCsv($this->csvExport->getCsvPath(static::COMPUTER_CSV_FILE_NAME), static::COMPUTER_CSV_HEADER, $input, $output);
        $this->checkCsv($this->csvExport->getCsvPath(static::PART_CSV_FILE_NAME), static::PART_CSV_HEADER, $input, $output);
        $this->checkCsv($this->csvExport->getCsvPath(static::HAS_PART_CSV_FILE_NAME), static::HAS_PART_CSV_HEADER, $input, $output);
        foreach (ComputerType::cases() as $type) {
            $this->indexComputersAndParts($type, $output);
        }
        $output->writeln('Indexed all computers and parts!');

        return Command::SUCCESS;
    }

    protected function indexComputersAndParts(ComputerType $type, OutputInterface $output): void
    {
        $output->writeln(sprintf('Indexing computers and parts for type %s...', $type->value));
        $this->browseFiles(
            $this->getRepoDir(static::HWINFO_REPO).DIRECTORY_SEPARATOR.$type->value,
            $output,
            function (SplFileInfo $file, bool $flush) use ($type): void {
                $computerId = $this->indexComputer($file, $type);
                $this->indexParts($computerId, $file);
                if ($flush) {
                    $this->computerRepository->flush(); // Doesn't matter which repository flush the changes
                    $this->csvExport->flush(static::COMPUTER_CSV_FILE_NAME);
                    $this->csvExport->flush(static::PART_CSV_FILE_NAME);
                    $this->csvExport->flush(static::HAS_PART_CSV_FILE_NAME);
                }
            }
        );
        $output->writeln(' Finished!');
    }

    protected function indexComputer(SplFileInfo $file, ComputerType $type): string
    {
        // {VENDOR}/{MODEL PREFIX}/{MODEL}/{HWID}/{OS}/{KERNEL}/{ARCH}/{PROBE ID}
        [$vendor, , $model, $hwid] = explode(DIRECTORY_SEPARATOR, $file->getRelativePathname());
        if (!$this->computerRepository->has($hwid)) {
            $computer = new Computer();
            $computer->id = $hwid;
            $computer->type = $type;
            $computer->vendor = $vendor;
            $computer->model = $model;
            $this->computerRepository->add($computer);
            $this->csvExport->addRow(static::COMPUTER_CSV_FILE_NAME, [$hwid, $type->value, $vendor, $model]);
        }

        return $hwid;
    }

    protected function indexParts(string $computerId, SplFileInfo $file): void
    {
        if ($count = preg_match_all('/Unique ID: ([^\s]+).*?Hardware Class: ([^\r\n]+).*?Model:.*?"([^"]+)".*?Config Status/s', $file->getContents(), $matches)) {
            for ($column = 0; $column < $count; ++$column) {
                @[, $id, $type, $model] = array_column($matches, $column);
                if (!$this->partRepository->has($id)) {
                    $part = new Part();
                    $part->id = $id;
                    $part->type = $type;
                    $part->model = $model;
                    $this->partRepository->add($part);
                    $this->csvExport->addRow(static::PART_CSV_FILE_NAME, [$id, $type, $model]);
                    $this->csvExport->addRow(static::HAS_PART_CSV_FILE_NAME, [$computerId, $id]);
                }
            }
        }
    }
}
