<?php

namespace App\Tests\Command;

use App\Entity\Computer;
use App\Entity\Probe;
use App\Enum\ComputerType;

class IndexComputerCommandTest extends CommandTestCase
{
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
        $this->assertComputer(
            '65DABC6ADAB3',
            ComputerType::ALL_IN_ONE,
            'Compaq',
            'CQ-A1',
            'F97345C4F4'
        );
        $this->assertComputer(
            '7873A4D65F33',
            ComputerType::CONVERTIBLE,
            'Toshiba',
            'PORTEGE X20W-E',
            '430A666F91'
        );
        $this->assertComputer(
            '95A3EC52C8D5',
            ComputerType::DESKTOP,
            'Compaq',
            'KY722AA-AB4 CQ3012L',
            '77D385BC09'
        );
        $this->assertComputer(
            '1F8B567A234D',
            ComputerType::MINI_PC,
            'ASUSTek Computer',
            'MINIPC PN50',
            '56597849BA'
        );
        $this->assertComputer(
            '1FEF768DB791',
            ComputerType::NOTEBOOK,
            'Lenovo',
            'E51-80 80QB',
            'CA5466D80A'
        );
        $this->assertComputer(
            'A2C964854704',
            ComputerType::SERVER,
            'Lenovo',
            'ThinkServer TD230 102919U',
            'B273789224'
        );
        $this->assertComputer(
            '2DD4B730FC08',
            ComputerType::STICK_PC,
            'Lenovo',
            'IdeaCentre Stick 300-01IBY 90ER0005RN',
            '28B902C9B7'
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
}
