<?php

namespace App\Csv\Repository;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EthernetPciCardRepository extends AbstractRepository
{
    public function __construct(
        #[Autowire('%app.ethernet_csv_path%')]
        protected string $ethernetCsvPath
    ) {
    }

    public function getCsvPath(): string
    {
        return $this->ethernetCsvPath;
    }

    public function setCsvPath(string $path): void
    {
        $this->ethernetCsvPath = $path;
    }
}
