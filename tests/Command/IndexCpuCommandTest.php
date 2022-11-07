<?php

namespace App\Tests\Command;

use App\Command\IndexCpuCommand;
use App\Csv\Repository\CpuRepository;
use App\Entity\Cpu;
use App\Enum\CpuVendor;

class IndexCpuCommandTest extends CommandTestCase
{
    protected array $data = [
        [
            'amd-ryzen-3-pro-1300-quad-core-processor',
            CpuVendor::AMD,
            'AMD Ryzen 3 PRO 1300 Quad-Core Processor',
            4,
            4,
            3500,
            1550,
            '2 MiB',
            '8 MiB',
            '98D52182A0',
        ],
        [
            'intel-core-i5-cpu-650-at-3-20ghz',
            CpuVendor::INTEL,
            'Intel(R) Core(TM) i5 CPU         650  @ 3.20GHz',
            2,
            4,
            3201,
            1200,
            '256K',
            '4096K',
            '0BB0F8B0AC',
        ],
    ];

    protected array $existingCpu = [
        'intel-atom-cpu-z3740-at-1-33ghz',
        'Intel',
        'Intel(R) Atom(TM) CPU  Z3740  @ 1.33GHz',
        '4',
        '4',
        '1866',
        '533',
        '1024K',
        '',
        '0CA0FC90D3',
    ];

    protected function getCommand(): string
    {
        return 'app:index-cpu';
    }

    protected function assertOutput(string $output): void
    {
        $this->assertStringContainsString('Updating repository...', $output);
        $this->assertStringContainsString('Indexing cpus for vendor AMD...', $output);
        $this->assertStringContainsString('Indexing cpus for vendor Intel...', $output);
        $this->assertStringContainsString('Indexed all cpus!', $output);
    }

    protected function assertParts(): void
    {
        foreach ($this->data as $cpu) {
            $this->assertCpu(...$cpu);
        }
    }

    protected function assertCpu(
        string $id,
        CpuVendor $vendor,
        string $model,
        int $cores,
        int $threads,
        int $maxSpeed,
        int $minSpeed,
        string $l2Cache,
        string $l3Cache
    ) {
        $cpu = $this->entityManager
            ->getRepository(Cpu::class)
            ->find($id)
        ;

        $this->assertInstanceOf(Cpu::class, $cpu);
        $this->assertSame($id, $cpu->id);
        $this->assertSame($vendor, $cpu->vendor);
        $this->assertSame($model, $cpu->model);
        $this->assertSame($cores, $cpu->cores);
        $this->assertSame($threads, $cpu->threads);
        $this->assertSame($maxSpeed, $cpu->maxSpeed);
        $this->assertSame($minSpeed, $cpu->minSpeed);
        $this->assertSame($l2Cache, $cpu->l2Cache);
        $this->assertSame($l3Cache, $cpu->l3Cache);
    }

    protected function assertCsv(): void
    {
        $this->assertEqualsCanonicalizing($this->getExpectedCsvData(), $this->loadCsv($this->fs->path('/cpu.csv')));
    }

    protected function getExpectedCsvData(): array
    {
        return [
            IndexCpuCommand::CPU_CSV_HEADER,
            $this->existingCpu,
            ...array_map(
                function (array $cpu): array {
                    list($cpuId, $vendor, $model, , , , , , , $probeId) = $cpu;

                    return [$cpuId, $vendor->value, $model, $probeId];
                },
                $this->data
            ),
        ];
    }

    protected function overrideCsvPath(): void
    {
        static::getContainer()->get(CpuRepository::class)->setCsvPath($this->fs->path('/cpu.csv'));
    }

    protected function getCommandInput(): array
    {
        return [0]; // Append csv file
    }

    protected function setUp(): void
    {
        parent::setUp();

        $file = fopen($this->fs->path('/cpu.csv'), 'w');
        fputcsv($file, IndexCpuCommand::CPU_CSV_HEADER);
        fputcsv($file, $this->existingCpu);
        fclose($file);
    }
}
