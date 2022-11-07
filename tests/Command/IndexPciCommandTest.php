<?php

namespace App\Tests\Command;

use App\Command\IndexPciCommand;
use App\Entity\EthernetPciCard;
use App\Entity\GraphicsCard;
use App\Entity\Printer;

class IndexPciCommandTest extends CommandTestCase
{
    protected array $graphicsCards = [
        // All in one
        [
            '1002-67df-1028-175c',
            'ATI Technologies Inc',
            'Dell',
            'Ellesmere [Radeon RX 470/480/570/570X/580/580X/590]',
            '071C584451',
        ],
        // Convertible
        [
            '8086-1616-103c-802d',
            'Intel Corporation',
            'Hewlett-Packard Company',
            'HD Graphics 5500',
            '4D1880C589',
        ],
        // Desktop
        [
            '10de-0de1-1462-2304',
            'nVidia Corporation',
            'Micro-Star International Co., Ltd.',
            'GF108 [GeForce GT 430]',
            'ED9D8A148D',
        ],
        [
            '10de-1201-1019-2036',
            'nVidia Corporation',
            'Elitegroup Computer Systems',
            'GF114 [GeForce GTX 560]',
            'EE8BDB8EC5',
        ],
        [
            '8086-0102-17aa-3070',
            'Intel Corporation',
            'Lenovo',
            '2nd Generation Core Processor Family Integrated Graphics Controller',
            'EE8BDB8EC5',
        ],
        // Mini PC
        [
            '8086-5a85-8086-2067',
            'Intel Corporation',
            'Intel Corporation',
            'HD Graphics 500',
            '4A3BB182A0',
        ],
        // Notebook
        [
            '1002-9712-1025-027d',
            'ATI Technologies Inc',
            'Acer Incorporated [ALI]',
            'RS880M [Mobility Radeon HD 4225/4250]',
            '326303D482',
        ],
        // Server
        [
            '1a03-2000-108e-484c',
            'ASPEED Technology Inc.',
            'Oracle/SUN',
            'AST1000/2000',
            'F048AD8494',
        ],
        // Stick PC
        [
            '8086-22b0-8086-7270',
            'Intel Corporation',
            'Intel Corporation',
            'Atom/Celeron/Pentium Processor x5-E8000/J3xxx/N3xxx Integrated Graphics Controller',
            '50585544F9',
        ],
    ];

    protected array $ethernetPciCards = [
        // All in one
        [
            '8086-15b7-1028-075c',
            'Intel Corporation',
            'Dell',
            'Ethernet Connection (2) I219-LM',
            '071C584451',
        ],
        // Desktop
        [
            '10ec-8168-1043-8385',
            'Realtek Semiconductor Co., Ltd.',
            'ASUSTeK Computer Inc.',
            'RTL8111/8168 PCI Express Gigabit Ethernet controller',
            'ED9D8A148D',
        ],
        [
            '1969-10a1-1849-10a1',
            'Qualcomm Atheros',
            'ASRock Incorporation',
            'QCA8171 Gigabit Ethernet',
            'E6DB55B378',
        ],
        [
            '8086-1502-17aa-3070',
            'Intel Corporation',
            'Lenovo',
            '82579LM Gigabit Network Connection (Lewisville)',
            'EE8BDB8EC5',
        ],
        // Mini PC
        [
            '10ec-8168-8086-2067',
            'Realtek Semiconductor Co., Ltd.',
            'Intel Corporation',
            'RTL8111/8168/8411 PCI Express Gigabit Ethernet Controller',
            '4A3BB182A0',
        ],
        // Notebook
        [
            '1969-1063-1025-027d',
            'Qualcomm Atheros',
            'Acer Incorporated [ALI]',
            'AR8131 Gigabit Ethernet',
            '326303D482',
        ],
        [
            '1969-2062-1025-0602',
            'Qualcomm Atheros',
            'Acer Incorporated [ALI]',
            'AR8152 v2.0 Fast Ethernet',
            '15B32DE383',
        ],
        // Server
        [
            '8086-10bc-108e-11bc',
            'Intel Corporation',
            'Oracle/SUN',
            '82571EB/82571GB Gigabit Ethernet Controller (Copper)',
            'F048AD8494',
        ],
        [
            '8086-10c9-108e-484c',
            'Intel Corporation',
            'Oracle/SUN',
            '82576 Gigabit Network Connection',
            'F048AD8494',
        ],
    ];

    protected array $printers = [
        // Desktop
        [
            'usb:03f0-c311',
            'HP',
            'ENVY 5530 series',
        ],
        [
            'usb:04b8-1106',
            'Epson',
            'EPSON ET-2550 Series',
        ],
        // Notebook
        [
            'usb:04f9-0428',
            'Brother Industries, Ltd',
            'HL-L2390DW',
        ],
    ];

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
        foreach ($this->graphicsCards as $graphicsCard) {
            $this->assertGraphicsCard(...$graphicsCard);
        }
        foreach ($this->ethernetPciCards as $ethernetPciCard) {
            $this->assertEthernetPciCard(...$ethernetPciCard);
        }
        foreach ($this->printers as $printer) {
            $this->assertPrinter(...$printer);
        }
    }

    protected function assertGraphicsCard(string $id, string $vendor, string $subVendor, string $device): void
    {
        $graphicsCard = $this->entityManager
            ->getRepository(GraphicsCard::class)
            ->find($id)
        ;

        $this->assertInstanceOf(GraphicsCard::class, $graphicsCard);
        $this->assertSame($id, $graphicsCard->id);
        $this->assertSame($vendor, $graphicsCard->vendor);
        $this->assertSame($device, $graphicsCard->device);
        $this->assertSame($subVendor, $graphicsCard->subVendor);
    }

    protected function assertPrinter(string $id, string $vendor, string $device): void
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

    protected function assertEthernetPciCard(string $id, string $vendor, string $subVendor, string $device): void
    {
        $ethernetPciCard = $this->entityManager
            ->getRepository(EthernetPciCard::class)
            ->find($id)
        ;

        $this->assertInstanceOf(EthernetPciCard::class, $ethernetPciCard);
        $this->assertSame($id, $ethernetPciCard->id);
        $this->assertSame($vendor, $ethernetPciCard->vendor);
        $this->assertSame($device, $ethernetPciCard->device);
        $this->assertSame($subVendor, $ethernetPciCard->subVendor);
    }

    protected function assertCsv(): void
    {
        $this->assertEqualsCanonicalizing([IndexPciCommand::GPU_CSV_HEADER, ...$this->graphicsCards], $this->loadCsv(IndexPciCommand::GPU_CSV_FILE_NAME));
        $this->assertEqualsCanonicalizing([IndexPciCommand::ETHERNET_CSV_HEADER, ...$this->ethernetPciCards], $this->loadCsv(IndexPciCommand::ETHERNET_CSV_FILE_NAME));
        $this->assertEqualsCanonicalizing([IndexPciCommand::PRINTER_CSV_HEADER, ...$this->printers], $this->loadCsv(IndexPciCommand::PRINTER_CSV_FILE_NAME));
    }
}
