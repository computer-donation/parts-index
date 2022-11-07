<?php

namespace App\Csv\Repository;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class GraphicsCardRepository extends AbstractRepository
{
    public function __construct(
        #[Autowire('%app.gpu_csv_path%')]
        protected string $gpuCsvPath
    ) {
    }

    public function getCsvPath(): string
    {
        return $this->gpuCsvPath;
    }

    public function setCsvPath(string $path): void
    {
        $this->gpuCsvPath = $path;
    }
}
