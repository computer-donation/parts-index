<?php

namespace App\Csv\Repository;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ComputerRepository extends AbstractRepository
{
    protected array $rows = [];

    public function __construct(
        #[Autowire('%app.computer_csv_path%')]
        protected string $computerCsvPath
    ) {
    }

    public function getCsvPath(): string
    {
        return $this->computerCsvPath;
    }

    public function setCsvPath(string $path): void
    {
        $this->computerCsvPath = $path;
    }
}
