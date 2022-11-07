<?php

namespace App\Csv\Repository;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MotherboardRepository extends AbstractRepository
{
    protected array $rows = [];

    public function __construct(
        #[Autowire('%app.motherboard_csv_path%')]
        protected string $motherboardCsvPath
    ) {
    }

    public function getCsvPath(): string
    {
        return $this->motherboardCsvPath;
    }

    public function setCsvPath(string $path): void
    {
        $this->motherboardCsvPath = $path;
    }
}
