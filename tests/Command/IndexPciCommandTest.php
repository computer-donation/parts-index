<?php

namespace App\Tests\Command;

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
            'Ellesmere [Radeon RX 470/480/570/570X/580/580X/590]',
            'Dell',
            '071C584451',
        ],
        // Convertible
        [
            '8086-1616-103c-802d',
            'Intel Corporation',
            'HD Graphics 5500',
            'Hewlett-Packard Company',
            '4D1880C589',
        ],
        // Desktop
        [
            '10de-0de1-1462-2304',
            'nVidia Corporation',
            'GF108 [GeForce GT 430]',
            'Micro-Star International Co., Ltd.',
            'ED9D8A148D',
        ],
        [
            '10de-1201-1019-2036',
            'nVidia Corporation',
            'GF114 [GeForce GTX 560]',
            'Elitegroup Computer Systems',
            'EE8BDB8EC5',
        ],
        [
            '8086-0102-17aa-3070',
            'Intel Corporation',
            '2nd Generation Core Processor Family Integrated Graphics Controller',
            'Lenovo',
            'EE8BDB8EC5',
        ],
        // Mini PC
        [
            '8086-5a85-8086-2067',
            'Intel Corporation',
            'HD Graphics 500',
            'Intel Corporation',
            '4A3BB182A0',
        ],
        // Notebook
        [
            '1002-9712-1025-027d',
            'ATI Technologies Inc',
            'RS880M [Mobility Radeon HD 4225/4250]',
            'Acer Incorporated [ALI]',
            '326303D482',
        ],
        // Server
        [
            '1a03-2000-108e-484c',
            'ASPEED Technology Inc.',
            'AST1000/2000',
            'Oracle/SUN',
            'F048AD8494',
        ],
        // Stick PC
        [
            '8086-22b0-8086-7270',
            'Intel Corporation',
            'Atom/Celeron/Pentium Processor x5-E8000/J3xxx/N3xxx Integrated Graphics Controller',
            'Intel Corporation',
            '50585544F9',
        ],
    ];

    protected array $ethernetPciCards = [
        // All in one
        [
            '8086-15b7-1028-075c',
            'Intel Corporation',
            'Ethernet Connection (2) I219-LM',
            'Dell',
            '071C584451',
        ],
        // Desktop
        [
            '1969-10a1-1849-10a1',
            'Qualcomm Atheros',
            'QCA8171 Gigabit Ethernet',
            'ASRock Incorporation',
            'E6DB55B378',
        ],
        [
            '10ec-8168-1043-8385',
            'Realtek Semiconductor Co., Ltd.',
            'RTL8111/8168 PCI Express Gigabit Ethernet controller',
            'ASUSTeK Computer Inc.',
            'ED9D8A148D',
        ],
        [
            '8086-1502-17aa-3070',
            'Intel Corporation',
            '82579LM Gigabit Network Connection (Lewisville)',
            'Lenovo',
            'EE8BDB8EC5',
        ],
        // Mini PC
        [
            '10ec-8168-8086-2067',
            'Realtek Semiconductor Co., Ltd.',
            'RTL8111/8168/8411 PCI Express Gigabit Ethernet Controller',
            'Intel Corporation',
            '4A3BB182A0',
        ],
        // Notebook
        [
            '1969-1063-1025-027d',
            'Qualcomm Atheros',
            'AR8131 Gigabit Ethernet',
            'Acer Incorporated [ALI]',
            '326303D482',
        ],
        [
            '1969-2062-1025-0602',
            'Qualcomm Atheros',
            'AR8152 v2.0 Fast Ethernet',
            'Acer Incorporated [ALI]',
            '15B32DE383',
        ],
        // Server
        [
            '8086-10bc-108e-11bc',
            'Intel Corporation',
            '82571EB/82571GB Gigabit Ethernet Controller (Copper)',
            'Oracle/SUN',
            'F048AD8494',
        ],
        [
            '8086-10c9-108e-484c',
            'Intel Corporation',
            '82576 Gigabit Network Connection',
            'Oracle/SUN',
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

    protected function assertGraphicsCard(string $id, string $vendor, string $device, string $subVendor)
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

    protected function assertEthernetPciCard(string $id, string $vendor, string $device, string $subVendor)
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

    protected function assertNodes(): void
    {
        foreach ($this->graphicsCards as $graphicsCard) {
            $this->assertGraphicsCardNode(...$graphicsCard);
        }
        foreach ($this->ethernetPciCards as $ethernetPciCard) {
            $this->assertEthernetPciCardNode(...$ethernetPciCard);
        }
        foreach ($this->printers as $printer) {
            $this->assertPrinterNode(...$printer);
        }
    }

    protected function assertGraphicsCardNode(string $id, string $vendor, string $device, string $subVendor): void
    {
        $result = $this->graphHelper->query('MATCH (gpu:GraphicsCard {id: $id}) RETURN gpu.vendor, gpu.device, gpu.subVendor', ['id' => $id])->getResultSet();
        $gpu = $result[0];
        $this->assertSame($vendor, $gpu[0]);
        $this->assertSame($device, $gpu[1]);
        $this->assertSame($subVendor, $gpu[2]);
    }

    protected function assertEthernetPciCardNode(string $id, string $vendor, string $device, string $subVendor): void
    {
        $result = $this->graphHelper->query('MATCH (ethernet:EthernetPciCard {id: $id}) RETURN ethernet.vendor, ethernet.device, ethernet.subVendor', ['id' => $id])->getResultSet();
        $ethernet = $result[0];
        $this->assertSame($vendor, $ethernet[0]);
        $this->assertSame($device, $ethernet[1]);
        $this->assertSame($subVendor, $ethernet[2]);
    }

    protected function assertPrinterNode(string $id, string $vendor, string $device): void
    {
        $result = $this->graphHelper->query('MATCH (printer:Printer {id: $id}) RETURN printer.vendor, printer.device', ['id' => $id])->getResultSet();
        $printer = $result[0];
        $this->assertSame($vendor, $printer[0]);
        $this->assertSame($device, $printer[1]);
    }

    protected function assertRelationships(): void
    {
        foreach ($this->graphicsCards as $graphicsCard) {
            $this->assertProbeGraphicsCardRelationship(...$graphicsCard);
        }
        foreach ($this->ethernetPciCards as $ethernetPciCard) {
            $this->assertProbeEthernetPciCardRelationship(...$ethernetPciCard);
        }
    }

    protected function assertProbeGraphicsCardRelationship(): void
    {
        $args = func_get_args();
        $gpuId = reset($args);
        $probeId = end($args);
        $result = $this->graphHelper->query('MATCH (gpu:GraphicsCard {id: $gpuId}) MATCH (probe:Probe {id: $probeId}) RETURN exists((probe)-[:HAS_GPU]->(gpu)) as hasRelationship', ['probeId' => $probeId, 'gpuId' => $gpuId])->getResultSet();
        $this->assertSame('true', $result[0][0]);
    }

    protected function assertProbeEthernetPciCardRelationship(): void
    {
        $args = func_get_args();
        $ethernetId = reset($args);
        $probeId = end($args);
        $result = $this->graphHelper->query('MATCH (ethernet:EthernetPciCard {id: $ethernetId}) MATCH (probe:Probe {id: $probeId}) RETURN exists((probe)-[:HAS_ETHERNET_PCI]->(ethernet)) as hasRelationship', ['probeId' => $probeId, 'ethernetId' => $ethernetId])->getResultSet();
        $this->assertSame('true', $result[0][0]);
    }
}
