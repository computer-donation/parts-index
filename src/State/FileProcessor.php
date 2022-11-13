<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Resource\File;
use App\Service\CsvExport;

class FileProcessor implements ProcessorInterface
{
    public function __construct(protected CsvExport $csvExport)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof File) {
            unlink($this->csvExport->getCsvPath($data->name.'.csv'));
        }
    }
}
