<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CsvExport
{
    protected array $rows = [];

    public function __construct(
        #[Autowire('%app.csv_export_dir%')]
        protected string $csvExportDir
    ) {
    }

    public function getCsvPath(string $fileName): string
    {
        return $this->csvExportDir.DIRECTORY_SEPARATOR.$fileName;
    }

    public function setCsvExportDir(string $path): void
    {
        $this->csvExportDir = $path;
    }

    public function addRow(string $fileName, array $row): void
    {
        $this->rows[$fileName][] = $row;
    }

    public function flush(string $fileName): void
    {
        if (!isset($this->rows[$fileName])) {
            return;
        }

        $csv = fopen($this->getCsvPath($fileName), 'a');
        foreach ($this->rows[$fileName] as $row) {
            fputcsv($csv, $row);
        }
        fclose($csv);
        $this->rows[$fileName] = [];
    }
}
