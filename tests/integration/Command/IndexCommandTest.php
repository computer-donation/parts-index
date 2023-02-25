<?php

namespace App\Tests\Command;

use App\Command\IndexCommand;
use App\Entity\Computer;
use App\Entity\Part;
use App\Enum\ComputerType;

class IndexCommandTest extends CommandTestCase
{
    protected array $computers = [
        // All in one
        [
            '2EB49941386F',
            ComputerType::ALL_IN_ONE,
            'Dell',
            'XPS 7760 AIO',
        ],
        // Convertible
        [
            '7B4A40B5DA5D',
            ComputerType::CONVERTIBLE,
            'Hewlett-Packard',
            'Spectre x360 Convertible 13',
        ],
        // Desktop
        [
            '2C240BA11E40',
            ComputerType::DESKTOP,
            'ASRock',
            'FM2A88M Extreme4+',
        ],
        // Mini PC
        [
            '078C47E8922E',
            ComputerType::MINI_PC,
            'Intel',
            'NUC6CAYB J23203-402',
        ],
        // Notebook
        [
            'CDA0D5D5D8AC',
            ComputerType::NOTEBOOK,
            'Acer',
            'Aspire 4540',
        ],
        // Server
        [
            '306C153487E1',
            ComputerType::SERVER,
            'Oracle',
            'Sun Fire X4270 M2 SERVER',
        ],
        // Stick PC
        [
            'A11940EE2CBD',
            ComputerType::STICK_PC,
            'AWOW',
            'Others',
        ],
    ];

    protected array $parts = [
        // All in one
        [
            'WnlC.OVTZhqsnEuD',
            'unknown',
            'Intel 100 Series/C230 Series Chipset Family MEI Controller #1',
        ],
        [
            'VCu0.+JcsK+yjXmE',
            'graphics card',
            'ATI Ellesmere [Radeon RX 470/480/570/570X/580/580X/590]',
        ],
        [
            'AhzA.4x2ahA85wG5',
            'network',
            'Intel Ethernet Connection (2) I219-LM',
        ],
        // Convertible
        [
            '3hqH.5vTEbhFbFBA',
            'sound',
            'Intel Broadwell-U Audio Controller',
        ],
        [
            '_Znp.hw1hbH1EzSC',
            'graphics card',
            'Intel HD Graphics 5500',
        ],
        [
            'MZfG.oppYvE9sVZ8',
            'usb controller',
            'Intel Wildcat Point-LP USB xHCI Controller',
        ],
        // Desktop
        [
            'UOJ9.ZhKSHlXL48B',
            'network',
            'Qualcomm Atheros QCA8171 Gigabit Ethernet',
        ],
        [
            'wilb.kZ_u24Hax33',
            'printer',
            'HP ENVY 5530 series',
        ],
        [
            'KRJj.sdGrt+0Ng37',
            'hub',
            'Terminus FE 2.1 7-port Hub',
        ],
        [
            'U0fP.Rw28o6FIR5A',
            'printer',
            'Epson EPSON ET-2550 Series',
        ],
        [
            'k4bc.YdoZZg0c8i6',
            'hub',
            'Linux Foundation 2.0 root hub',
        ],
        // Mini PC
        [
            'UOJ9.rNk52lZdwt0',
            'network',
            'Realtek RTL8111/8168/8411 PCI Express Gigabit Ethernet Controller',
        ],
        [
            '_Znp.qHzegI2GNs7',
            'graphics card',
            'Intel HD Graphics 500',
        ],
        [
            'uI_Q.JHfGqWl+dS2',
            'disk',
            'Generic SD/M.S.',
        ],
        // Notebook
        [
            'UOJ9.7pAo1Xl_uH9',
            'network',
            'Qualcomm Atheros AR8131 Gigabit Ethernet',
        ],
        [
            'ul7N._K8Zr0U2uGD',
            'graphics card',
            'ATI RS880M [Mobility Radeon HD 4225/4250]',
        ],
        [
            '5Dex.+wYY5UH6eG7',
            'sound',
            'ATI SBx00 Azalia (Intel HDA)',
        ],
        // Server
        [
            'USj7.Ke3G9sOdAV5',
            'bridge',
            'Microsemi / PMC / IDT PES24T6G2 PCI Express Gen2 Switch',
        ],
        [
            '2sHQ.MsbgkS25Bk8',
            'graphics card',
            'ASPEED AST1000/2000',
        ],
        [
            'GjA8.nnwMpZgQGeA',
            'network',
            'Oracle/SUN Quad Port Adapter',
        ],
        [
            'DkES.nnwMpZgQGeA',
            'network',
            'Oracle/SUN Quad Port Adapter',
        ],
        [
            'TRND.OeyupWtxS+9',
            'network',
            'Intel 82576 Gigabit Network Connection',
        ],
        // Stick PC
        [
            'gZD2.NvD8dYoxtr1',
            'unknown',
            'Intel Atom/Celeron/Pentium Processor x5-E8000/J3xxx/N3xxx Series Power Management Controller',
        ],
        [
            '_Znp.TsVUsUC2nt3',
            'graphics card',
            'Intel Atom/Celeron/Pentium Processor x5-E8000/J3xxx/N3xxx Integrated Graphics Controller',
        ],
        [
            'MZfG.UxwsbGTKwQC',
            'usb controller',
            'Intel Atom/Celeron/Pentium Processor x5-E8000/J3xxx/N3xxx Series USB xHCI Controller',
        ],
    ];

    protected array $hasParts = [
        // All in one
        '2EB49941386F' => [
            'WnlC.OVTZhqsnEuD',
            'VCu0.+JcsK+yjXmE',
            'AhzA.4x2ahA85wG5',
        ],
        // Convertible
        '7B4A40B5DA5D' => [
            '3hqH.5vTEbhFbFBA',
            '_Znp.hw1hbH1EzSC',
            'MZfG.oppYvE9sVZ8',
        ],
        // Desktop
        '2C240BA11E40' => [
            'UOJ9.ZhKSHlXL48B',
            'wilb.kZ_u24Hax33',
            'KRJj.sdGrt+0Ng37',
            'U0fP.Rw28o6FIR5A',
            'k4bc.YdoZZg0c8i6',
        ],
        // Mini PC
        '078C47E8922E' => [
            'UOJ9.rNk52lZdwt0',
            '_Znp.qHzegI2GNs7',
            'uI_Q.JHfGqWl+dS2',
        ],
        // Notebook
        'CDA0D5D5D8AC' => [
            'UOJ9.7pAo1Xl_uH9',
            'ul7N._K8Zr0U2uGD',
            '5Dex.+wYY5UH6eG7',
        ],
        // Server
        '306C153487E1' => [
            'USj7.Ke3G9sOdAV5',
            '2sHQ.MsbgkS25Bk8',
            'GjA8.nnwMpZgQGeA',
            'DkES.nnwMpZgQGeA',
            'TRND.OeyupWtxS+9',
        ],
        // Stick PC
        'A11940EE2CBD' => [
            'gZD2.NvD8dYoxtr1',
            '_Znp.TsVUsUC2nt3',
            'MZfG.UxwsbGTKwQC',
        ],
    ];

    protected function getCommand(): string
    {
        return 'app:index';
    }

    protected function assertOutput(string $output): void
    {
        $this->assertStringContainsString('Updating repository...', $output);
        $this->assertStringContainsString('Indexing computers and parts for type All In One...', $output);
        $this->assertStringContainsString('Indexing computers and parts for type Convertible...', $output);
        $this->assertStringContainsString('Indexing computers and parts for type Desktop...', $output);
        $this->assertStringContainsString('Indexing computers and parts for type Mini Pc...', $output);
        $this->assertStringContainsString('Indexing computers and parts for type Notebook...', $output);
        $this->assertStringContainsString('Indexing computers and parts for type Server...', $output);
        $this->assertStringContainsString('Indexing computers and parts for type Stick Pc...', $output);
        $this->assertStringContainsString('Indexed all computers and parts!', $output);
    }

    protected function assertDatabase(): void
    {
        $this->assertComputers();
        $this->assertParts();
    }

    protected function assertComputers(): void
    {
        foreach ($this->computers as $computer) {
            $this->assertComputer(...$computer);
        }
    }

    protected function assertParts(): void
    {
        foreach ($this->parts as $part) {
            $this->assertPart(...$part);
        }
    }

    protected function assertComputer(string $id, ComputerType $type, string $vendor, string $model)
    {
        $computer = $this->entityManager
            ->getRepository(Computer::class)
            ->find($id)
        ;

        $this->assertInstanceOf(Computer::class, $computer);
        $this->assertSame($id, $computer->id);
        $this->assertSame($type, $computer->type);
        $this->assertSame($vendor, $computer->vendor);
        $this->assertSame($model, $computer->model);
    }

    protected function assertPart(string $id, string $type, string $model)
    {
        $part = $this->entityManager
            ->getRepository(Part::class)
            ->find($id)
        ;

        $this->assertInstanceOf(Part::class, $part);
        $this->assertSame($id, $part->id);
        $this->assertSame($type, $part->type);
        $this->assertSame($model, $part->model);
    }

    protected function assertCsv(): void
    {
        $this->assertComputerCsv();
        $this->assertPartCsv();
        $this->assertHasPartCsv();
    }

    protected function assertComputerCsv(): void
    {
        $this->assertEqualsCanonicalizing([
            IndexCommand::COMPUTER_CSV_HEADER,
            ...array_map(
                function (array $computer): array {
                    list($id, $type, $vendor, $model) = $computer;

                    return [$id, $type->value, $vendor, $model];
                },
                $this->computers
            ),
        ], $this->loadCsv(IndexCommand::COMPUTER_CSV_FILE_NAME));
    }

    protected function assertPartCsv(): void
    {
        $this->assertEqualsCanonicalizing([
            IndexCommand::PART_CSV_HEADER,
            ...$this->parts,
        ], $this->loadCsv(IndexCommand::PART_CSV_FILE_NAME));
    }

    protected function assertHasPartCsv(): void
    {
        $rows = [];
        foreach ($this->hasParts as $computerId => $parts) {
            foreach ($parts as $partId) {
                $rows[] = [$computerId, $partId];
            }
        }
        $this->assertEqualsCanonicalizing([
            IndexCommand::HAS_PART_CSV_HEADER,
            ...$rows,
        ], $this->loadCsv(IndexCommand::HAS_PART_CSV_FILE_NAME));
    }
}
