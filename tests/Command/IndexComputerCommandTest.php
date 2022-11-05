<?php

namespace App\Tests\Command;

use App\Entity\Computer;
use App\Enum\ComputerType;

class IndexComputerCommandTest extends CommandTestCase
{
    protected array $data = [
        // Sensors dir
        [
            '65DABC6ADAB3',
            ComputerType::ALL_IN_ONE,
            'Compaq',
            'CQ-A1',
            'F97345C4F4',
        ],
        [
            '7873A4D65F33',
            ComputerType::CONVERTIBLE,
            'Toshiba',
            'PORTEGE X20W-E',
            '430A666F91',
        ],
        [
            '95A3EC52C8D5',
            ComputerType::DESKTOP,
            'Compaq',
            'KY722AA-AB4 CQ3012L',
            '77D385BC09',
        ],
        [
            '1F8B567A234D',
            ComputerType::MINI_PC,
            'ASUSTek Computer',
            'MINIPC PN50',
            '56597849BA',
        ],
        [
            '1FEF768DB791',
            ComputerType::NOTEBOOK,
            'Lenovo',
            'E51-80 80QB',
            'CA5466D80A',
        ],
        [
            'A2C964854704',
            ComputerType::SERVER,
            'Lenovo',
            'ThinkServer TD230 102919U',
            'B273789224',
        ],
        [
            '2DD4B730FC08',
            ComputerType::STICK_PC,
            'Lenovo',
            'IdeaCentre Stick 300-01IBY 90ER0005RN',
            '28B902C9B7',
        ],
        // HWInfo dir
        [
            '2EB49941386F',
            ComputerType::ALL_IN_ONE,
            'Dell',
            'XPS 7760 AIO',
            '071C584451',
        ],
        [
            '7B4A40B5DA5D',
            ComputerType::CONVERTIBLE,
            'Hewlett-Packard',
            'Spectre x360 Convertible 13',
            '4D1880C589',
        ],
        [
            '15D24AEF63B0',
            ComputerType::DESKTOP,
            'ASUSTek Computer',
            'M4A78 PLUS',
            'ED9D8A148D',
        ],
        [
            '078C47E8922E',
            ComputerType::MINI_PC,
            'Intel',
            'NUC6CAYB J23203-402',
            '4A3BB182A0',
        ],
        [
            'CDA0D5D5D8AC',
            ComputerType::NOTEBOOK,
            'Acer',
            'Aspire 4540',
            '326303D482',
        ],
        [
            '306C153487E1',
            ComputerType::SERVER,
            'Oracle',
            'Sun Fire X4270 M2 SERVER',
            'F048AD8494',
        ],
        [
            'A11940EE2CBD',
            ComputerType::STICK_PC,
            'AWOW',
            'Others',
            '50585544F9',
        ],
    ];

    protected function getCommand(): string
    {
        return 'app:index-computer';
    }

    protected function assertOutput(string $output): void
    {
        $this->assertStringContainsString('Updating repository...', $output);
        $this->assertStringContainsString('Indexing computers for type All In One...', $output);
        $this->assertStringContainsString('Indexing computers for type Convertible...', $output);
        $this->assertStringContainsString('Indexing computers for type Desktop...', $output);
        $this->assertStringContainsString('Indexing computers for type Mini Pc...', $output);
        $this->assertStringContainsString('Indexing computers for type Notebook...', $output);
        $this->assertStringContainsString('Indexing computers for type Server...', $output);
        $this->assertStringContainsString('Indexing computers for type Stick Pc...', $output);
        $this->assertStringContainsString('Indexed all computers!', $output);
    }

    protected function assertParts(): void
    {
        foreach ($this->data as $computer) {
            $this->assertComputer(...$computer);
        }
    }

    protected function assertComputer(string $id, ComputerType $type, string $vendor, string $model, string $probeId)
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

    protected function assertNodes(): void
    {
        foreach ($this->data as $computer) {
            $this->assertComputerNode(...$computer);
        }
    }

    protected function assertComputerNode(string $id, ComputerType $type, string $vendor, string $model): void
    {
        $result = $this->graphHelper->query('MATCH (computer:Computer {id: $id}) RETURN computer.type, computer.vendor, computer.model', ['id' => $id])->getResultSet();
        $computer = $result[0];
        $this->assertSame($type->value, $computer[0]);
        $this->assertSame($vendor, $computer[1]);
        $this->assertSame($model, $computer[2]);
    }

    protected function assertRelationships(): void
    {
        foreach ($this->data as $computer) {
            $this->assertProbeComputerRelationship(...$computer);
        }
    }

    protected function assertProbeComputerRelationship(): void
    {
        $args = func_get_args();
        $computerId = reset($args);
        $probeId = end($args);
        $result = $this->graphHelper->query('MATCH (computer:Computer {id: $computerId}) MATCH (probe:Probe {id: $probeId}) RETURN exists((probe)-[:HAS_COMPUTER]->(computer)) as hasRelationship', ['computerId' => $computerId, 'probeId' => $probeId])->getResultSet();
        $this->assertSame('true', $result[0][0]);
    }
}
