<?php

namespace App\Csv\Repository;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PrinterRepository extends AbstractRepository
{
    public function __construct(
        #[Autowire('%app.printer_csv_path%')]
        protected string $printerCsvPath
    ) {
    }

    public function getCsvPath(): string
    {
        return $this->printerCsvPath;
    }

    public function setCsvPath(string $path): void
    {
        $this->printerCsvPath = $path;
    }
}
