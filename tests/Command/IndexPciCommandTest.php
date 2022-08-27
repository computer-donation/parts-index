<?php

namespace App\Tests\Command;

use App\Entity\GraphicsCard;
use App\Entity\Printer;
use App\Entity\Probe;

class IndexPciCommandTest extends CommandTestCase
{
    protected function getCommand(): string
    {
        return 'app:index-pci';
    }

    protected function assertOutput(string $output): void
    {
        $this->assertStringContainsString('Updating repository...', $output);
        $this->assertStringContainsString('Indexing pci devices for type All In One...', $output);
        $this->assertStringContainsString('Indexing pci devices for type Convertible...', $output);
        $this->assertStringContainsString('Indexing pci devices for type Desktop...', $output);
        $this->assertStringContainsString('Indexing pci devices for type Mini Pc...', $output);
        $this->assertStringContainsString('Indexing pci devices for type Notebook...', $output);
        $this->assertStringContainsString('Indexing pci devices for type Server...', $output);
        $this->assertStringContainsString('Indexing pci devices for type Stick Pc...', $output);
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
        $this->assertGraphicsCard(
            '10de-0de1-1462-2304',
            'nVidia Corporation',
            'GF108 [GeForce GT 430]',
            'Micro-Star International Co., Ltd.',
            'ED9D8A148D'
        );
        $this->assertGraphicsCard(
            '10de-1201-1019-2036',
            'nVidia Corporation',
            'GF114 [GeForce GTX 560]',
            'Elitegroup Computer Systems',
            'EE8BDB8EC5'
        );
        $this->assertGraphicsCard(
            '8086-0102-17aa-3070',
            'Intel Corporation',
            '2nd Generation Core Processor Family Integrated Graphics Controller',
            'Lenovo',
            'EE8BDB8EC5'
        );
        $this->assertPrinter(
            'usb:03f0-c311',
            'HP',
            'ENVY 5530 series'
        );
        $this->assertPrinter(
            'usb:04b8-1106',
            'Epson',
            'EPSON ET-2550 Series'
        );
    }

    protected function assertMiniPcParts(): void
    {
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
        $this->assertGraphicsCard(
            '1002-9712-1025-027d',
            'ATI Technologies Inc',
            'RS880M [Mobility Radeon HD 4225/4250]',
            'Acer Incorporated [ALI]',
            '326303D482'
        );
        $this->assertPrinter(
            'usb:04f9-0428',
            'Brother Industries, Ltd',
            'HL-L2390DW'
        );
    }

    protected function assertServerParts(): void
    {
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
        $this->assertGraphicsCard(
            '8086-22b0-8086-7270',
            'Intel Corporation',
            'Atom/Celeron/Pentium Processor x5-E8000/J3xxx/N3xxx Integrated Graphics Controller',
            'Intel Corporation',
            '50585544F9'
        );
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
        $this->assertContains($graphicsCard, $probe->getGraphicsCards());
        $this->assertContains($probe, $graphicsCard->getProbes());
    }

    protected function assertPrinter(string $id, string $vendor, string $device)
    {
        $printer = $this->entityManager
            ->getRepository(Printer::class)
            ->find($id)
        ;

        $this->assertInstanceOf(Printer::class, $printer);
        $this->assertSame($id, $printer->id);
        $this->assertSame($vendor, $printer->vendor);
        $this->assertSame($device, $printer->device);
    }
}
