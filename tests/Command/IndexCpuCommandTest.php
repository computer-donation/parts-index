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
            'amd-23-1-1-ryzen-3-pro-1300-quad-core',
            CpuVendor::AMD,
            'Ryzen 3 PRO 1300 Quad-Core',
            '98D52182A0'
        );
        $this->assertCpu(
            'intel-6-37-2-core-i5-650',
            CpuVendor::INTEL,
            'Core i5 650',
            '0BB0F8B0AC'
        );
    }

    protected function assertCpu(string $id, CpuVendor $vendor, string $model, string $probeId)
    {
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
        $this->assertSame($cpu, $probe->cpu);
    }
}
