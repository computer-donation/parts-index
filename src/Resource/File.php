<?php

namespace App\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Controller\DownloadFile;
use App\State\FileProvider;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[ApiResource(operations: [
    new Get(
        controller: DownloadFile::class,
        input: false,
        output: BinaryFileResponse::class,
        provider: FileProvider::class,
        outputFormats: ['csv'],
        openapiContext: [
            'summary' => 'Download exported csv file',
            'description' => 'Download exported csv file',
            'responses' => [
                '200' => [
                    'description' => 'OK',
                    'content' => [
                        'text/csv' => [
                            'schema' => [
                                'type' => 'string',
                                'example' => <<<CSV
                                    #cpu.csv
                                    cpuId,vendor,model,probeId
                                    amd-3015e-with-radeon-graphics,AMD,"AMD 3015e with Radeon Graphics",CE0D33750C
                                    intel-xeon-cpu-2-40ghz,Intel,"Intel(R) Xeon(TM) CPU 2.40GHz",34867AD122
                                    CSV,
                            ],
                        ],
                    ],
                ],
            ],
        ]
    ),
])]
class File
{
    #[ApiProperty(identifier: true)]
    public string $name;
}
