<?php

namespace App\Tests\Command;

use App\Entity\EthernetPciCard;
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
        $this->assertStringContainsString('Indexing computers and pci devices for type All In One...', $output);
        $this->assertStringContainsString('Indexing computers and pci devices for type Convertible...', $output);
        $this->assertStringContainsString('Indexing computers and pci devices for type Desktop...', $output);
        $this->assertStringContainsString('Indexing computers and pci devices for type Mini Pc...', $output);
        $this->assertStringContainsString('Indexing computers and pci devices for type Notebook...', $output);
        $this->assertStringContainsString('Indexing computers and pci devices for type Server...', $output);
        $this->assertStringContainsString('Indexing computers and pci devices for type Stick Pc...', $output);
        $this->assertStringContainsString('Indexed all computers and pci devices!', $output);
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
        $this->assertEthernetPciCard(
            '8086-15b7-1028-075c',
            'Intel Corporation',
            'Ethernet Connection (2) I219-LM',
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
        $this->assertEthernetPciCard(
            '1969-10a1-1849-10a1',
            'Qualcomm Atheros',
            'QCA8171 Gigabit Ethernet',
            'ASRock Incorporation',
            'E6DB55B378'
        );
        $this->assertEthernetPciCard(
            '10ec-8168-1043-8385',
            'Realtek Semiconductor Co., Ltd.',
            'RTL8111/8168 PCI Express Gigabit Ethernet controller',
            'ASUSTeK Computer Inc.',
            'ED9D8A148D'
        );
        $this->assertEthernetPciCard(
            '8086-1502-17aa-3070',
            'Intel Corporation',
            '82579LM Gigabit Network Connection (Lewisville)',
            'Lenovo',
            'EE8BDB8EC5'
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
        $this->assertEthernetPciCard(
            '10ec-8168-8086-2067',
            'Realtek Semiconductor Co., Ltd.',
            'RTL8111/8168/8411 PCI Express Gigabit Ethernet Controller',
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
        $this->assertEthernetPciCard(
            '1969-1063-1025-027d',
            'Qualcomm Atheros',
            'AR8131 Gigabit Ethernet',
            'Acer Incorporated [ALI]',
            '326303D482'
        );
        $this->assertEthernetPciCard(
            '1969-2062-1025-0602',
            'Qualcomm Atheros',
            'AR8152 v2.0 Fast Ethernet',
            'Acer Incorporated [ALI]',
            '15B32DE383'
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
        $this->assertEthernetPciCard(
            '8086-10bc-108e-11bc',
            'Intel Corporation',
            '82571EB/82571GB Gigabit Ethernet Controller (Copper)',
            'Oracle/SUN',
            'F048AD8494'
        );
        $this->assertEthernetPciCard(
            '8086-10c9-108e-484c',
            'Intel Corporation',
            '82576 Gigabit Network Connection',
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
        $probe = $this->getProbe($probeId);

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

    protected function assertEthernetPciCard(string $id, string $vendor, string $device, string $subVendor, string $probeId)
    {
        $ethernetPciCard = $this->entityManager
            ->getRepository(EthernetPciCard::class)
            ->find($id)
        ;
        $probe = $this->getProbe($probeId);

        $this->assertInstanceOf(EthernetPciCard::class, $ethernetPciCard);
        $this->assertSame($id, $ethernetPciCard->id);
        $this->assertSame($vendor, $ethernetPciCard->vendor);
        $this->assertSame($device, $ethernetPciCard->device);
        $this->assertSame($subVendor, $ethernetPciCard->subVendor);
        $this->assertContains($ethernetPciCard, $probe->getEthernetPciCards());
        $this->assertContains($probe, $ethernetPciCard->getProbes());
    }

    protected function getProbe(string $id): Probe
    {
        return $this->entityManager
            ->getRepository(Probe::class)
            ->find($id)
        ;
    }
}
