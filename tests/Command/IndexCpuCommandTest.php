<?php

namespace App\Tests\Command;

use App\Entity\Cpu;
use App\Entity\Probe;
use App\Enum\CpuVendor;

class IndexCpuCommandTest extends CommandTestCase
{
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
        $this->assertCpu(
            'amd-ryzen-3-pro-1300-quad-core-processor',
            CpuVendor::AMD,
            'AMD Ryzen 3 PRO 1300 Quad-Core Processor',
            4,
            4,
            3500,
            1550,
            '2 MiB',
            '8 MiB',
            '98D52182A0'
        );
        $this->assertCpu(
            'intel-core-i5-cpu-650-at-3-20ghz',
            CpuVendor::INTEL,
            'Intel(R) Core(TM) i5 CPU         650  @ 3.20GHz',
            2,
            4,
            3201,
            1200,
            '256K',
            '4096K',
            '0BB0F8B0AC'
        );
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
        string $l3Cache,
        string $probeId
    ) {
        $cpu = $this->entityManager
            ->getRepository(Cpu::class)
            ->find($id)
        ;

        $probe = $this->entityManager
            ->getRepository(Probe::class)
            ->find($probeId)
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
        $this->assertSame($cpu, $probe->cpu);
    }
}
