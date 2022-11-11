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

    public function getCsvPath(string $filename): string
    {
        return $this->csvExportDir.DIRECTORY_SEPARATOR.$filename;
    }

    public function setCsvExportDir(string $path): void
    {
        $this->csvExportDir = $path;
    }

    public function addRow(string $filename, array $row): void
    {
        $this->rows[$filename][] = $row;
    }

    public function flush(string $filename): void
    {
        if (!isset($this->rows[$filename])) {
            return;
        }

        $csv = fopen($this->getCsvPath($filename), 'a');
        foreach ($this->rows[$filename] as $row) {
            fputcsv($csv, $row);
        }
        fclose($csv);
        $this->rows[$filename] = [];
    }
}
