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
