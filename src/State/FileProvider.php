<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Resource\File;
use App\Service\CsvExport;

final class FileProvider implements ProviderInterface
{
    public function __construct(protected CsvExport $csvExport)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?object
    {
        $name = $uriVariables['name'];
        if (file_exists($this->csvExport->getCsvPath($name.'.csv'))) {
            $file = new File();
            $file->name = $name;

            return $file;
        }

        return null;
    }
}
