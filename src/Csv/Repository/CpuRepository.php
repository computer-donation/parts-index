<?php

namespace App\Csv\Repository;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CpuRepository extends AbstractRepository
{
    public function __construct(
        #[Autowire('%app.cpu_csv_path%')]
        protected string $cpuCsvPath
    ) {
    }

    public function getCsvPath(): string
    {
        return $this->cpuCsvPath;
    }

    public function setCsvPath(string $path): void
    {
        $this->cpuCsvPath = $path;
    }
}
