<?php

namespace App\Tests\Command;

use App\Entity\Computer;
use App\Entity\GraphicsCard;
use App\Entity\Probe;
use App\Enum\ComputerType;

class IndexPciCommandTest extends CommandTestCase
{
    protected function getCommand(): string
    {
        return 'app:index-pci';
    }

    protected function assertOutput(string $output): void
    {
        $this->assertStringContainsString('Updating repository...', $output);
        $this->assertStringContainsString('Indexing computers and pci devices for type All In One...', $output);
        $this->assertStringContainsString('Indexing computers and pci devices for type Convertible...', $output);
        $this->assertStringContainsString('Indexing computers and pci devices for type Desktop...', $output);
        $this->assertStringContainsString('Indexing computers and pci devices for type Mini Pc...', $output);
        $this->assertStringContainsString('Indexing computers and pci devices for type Notebook...', $output);
        $this->assertStringContainsString('Indexing computers and pci devices for type Server...', $output);
        $this->assertStringContainsString('Indexing computers and pci devices for type Stick Pc...', $output);
        $this->assertStringContainsString('Indexed all pci devices!', $output);
    }

    protected function assertParts(): void
    {
        $this->assertAllInOneParts();
        $this->assertConvertibleParts();
        $this->assertDesktopParts();
        $this->assertMiniPcParts();
        $this->assertNotebookParts();
        $this->assertServerParts();
        $this->assertStickPcParts();
    }

    protected function assertAllInOneParts(): void
    {
        $this->assertComputer(
            '2EB49941386F',
            ComputerType::ALL_IN_ONE,
            'Dell',
            'XPS 7760 AIO',
            '071C584451'
        );
        $this->assertGraphicsCard(
            '1002-67df-1028-175c',
            'ATI Technologies Inc',
            'Ellesmere [Radeon RX 470/480/570/570X/580/580X/590]',
            'Dell',
            '071C584451'
        );
    }

    protected function assertConvertibleParts(): void
    {
        $this->assertComputer(
            '7B4A40B5DA5D',
            ComputerType::CONVERTIBLE,
            'Hewlett-Packard',
            'Spectre x360 Convertible 13',
            '4D1880C589'
        );
        $this->assertGraphicsCard(
            '8086-1616-103c-802d',
            'Intel Corporation',
            'HD Graphics 5500',
            'Hewlett-Packard Company',
            '4D1880C589'
        );
    }

    protected function assertDesktopParts(): void
    {
        $this->assertComputer(
            '15D24AEF63B0',
            ComputerType::DESKTOP,
            'ASUSTek Computer',
            'M4A78 PLUS',
            'ED9D8A148D'
        );
        $this->assertGraphicsCard(
            '10de-0de1-1462-2304',
            'nVidia Corporation',
            'GF108 [GeForce GT 430]',
            'Micro-Star International Co., Ltd.',
            'ED9D8A148D'
        );
    }

    protected function assertMiniPcParts(): void
    {
        $this->assertComputer(
            '078C47E8922E',
            ComputerType::MINI_PC,
            'Intel',
            'NUC6CAYB J23203-402',
            '4A3BB182A0'
        );
        $this->assertGraphicsCard(
            '8086-5a85-8086-2067',
            'Intel Corporation',
            'HD Graphics 500',
            'Intel Corporation',
            '4A3BB182A0'
        );
    }

    protected function assertNotebookParts(): void
    {
        $this->assertComputer(
            'CDA0D5D5D8AC',
            ComputerType::NOTEBOOK,
            'Acer',
            'Aspire 4540',
            '326303D482'
        );
        $this->assertGraphicsCard(
            '1002-9712-1025-027d',
            'ATI Technologies Inc',
            'RS880M [Mobility Radeon HD 4225/4250]',
            'Acer Incorporated [ALI]',
            '326303D482'
        );
    }

    protected function assertServerParts(): void
    {
        $this->assertComputer(
            '306C153487E1',
            ComputerType::SERVER,
            'Oracle',
            'Sun Fire X4270 M2 SERVER',
            'F048AD8494'
        );
        $this->assertGraphicsCard(
            '1a03-2000-108e-484c',
            'ASPEED Technology Inc.',
            'AST1000/2000',
            'Oracle/SUN',
            'F048AD8494'
        );
    }

    protected function assertStickPcParts(): void
    {
        $this->assertComputer(
            'A11940EE2CBD',
            ComputerType::STICK_PC,
            'AWOW',
            'Others',
            '50585544F9'
        );
        $this->assertGraphicsCard(
            '8086-22b0-8086-7270',
            'Intel Corporation',
            'Atom/Celeron/Pentium Processor x5-E8000/J3xxx/N3xxx Integrated Graphics Controller',
            'Intel Corporation',
            '50585544F9'
        );
    }

    protected function assertComputer(string $id, ComputerType $type, string $vendor, string $model, string $probeId)
    {
        $computer = $this->entityManager
            ->getRepository(Computer::class)
            ->find($id)
        ;

        $probe = $this->entityManager
            ->getRepository(Probe::class)
            ->find($probeId)
        ;

        $this->assertInstanceOf(Computer::class, $computer);
        $this->assertSame($id, $computer->id);
        $this->assertSame($type, $computer->type);
        $this->assertSame($vendor, $computer->vendor);
        $this->assertSame($model, $computer->model);
        $this->assertSame($computer, $probe->computer);
    }

    protected function assertGraphicsCard(string $id, string $vendor, string $device, string $subVendor, string $probeId)
    {
        $graphicsCard = $this->entityManager
            ->getRepository(GraphicsCard::class)
            ->find($id)
        ;

        $probe = $this->entityManager
            ->getRepository(Probe::class)
            ->find($probeId)
        ;

        $this->assertInstanceOf(GraphicsCard::class, $graphicsCard);
        $this->assertSame($id, $graphicsCard->id);
        $this->assertSame($vendor, $graphicsCard->vendor);
        $this->assertSame($device, $graphicsCard->device);
        $this->assertSame($subVendor, $graphicsCard->subVendor);
        $this->assertSame($graphicsCard, $probe->graphicsCard);
    }
}
