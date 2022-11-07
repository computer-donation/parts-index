<?php

namespace App\Csv\Repository;

abstract class AbstractRepository
{
    protected array $rows = [];

    public function addRow(array $row): void
    {
        $this->rows[] = $row;
    }

    public function flush(): void
    {
        $csv = fopen($this->getCsvPath(), 'a');
        foreach ($this->rows as $row) {
            fputcsv($csv, $row);
        }
        fclose($csv);
        $this->rows = [];
    }

    abstract public function getCsvPath(): string;

    abstract public function setCsvPath(string $path): void;
}
