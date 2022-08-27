<?php

namespace App\Tests\Command;

use App\Entity\Computer;
use App\Entity\Motherboard;
use App\Enum\ComputerType;

class IndexMotherboardCommandTest extends CommandTestCase
{
    protected function getCommand(): string
    {
        return 'app:index-motherboard';
    }

    protected function initDatabase(): void
    {
        parent::initDatabase();
        $ids = ['3FEBF400F4CB', '3451354907A7', '7584AD78724E', 'FCA7A82BFDE5', 'BA15B44DC033', '50F52177DF45', '2DD4B730FC08'];
        foreach ($ids as $id) {
            $computer = new Computer();
            $computer->id = $id;
            $computer->type = ComputerType::ALL_IN_ONE;
            $computer->vendor = '';
            $computer->model = '';
            $this->entityManager->persist($computer);
        }
        $this->entityManager->flush();
    }

    protected function assertOutput(string $output): void
    {
        $this->assertStringContainsString('Updating repository...', $output);
        $this->assertStringContainsString('Indexing motherboards for type All In One...', $output);
        $this->assertStringContainsString('Indexing motherboards for type Convertible...', $output);
        $this->assertStringContainsString('Indexing motherboards for type Desktop...', $output);
        $this->assertStringContainsString('Indexing motherboards for type Mini Pc...', $output);
        $this->assertStringContainsString('Indexing motherboards for type Notebook...', $output);
        $this->assertStringContainsString('Indexing motherboards for type Server...', $output);
        $this->assertStringContainsString('Indexing motherboards for type Stick Pc...', $output);
        $this->assertStringContainsString('Indexed all motherboards!', $output);
    }

    protected function assertParts(): void
    {
        $this->assertMotherboard(
            'asustek-computer-inc.-v241da-1.0',
            'ASUSTeK COMPUTER INC.',
            'V241DA',
            '1.0',
            '3FEBF400F4CB'
        );
        $this->assertMotherboard(
            'kbl-buzz_kl-v1.07',
            'KBL',
            'Buzz_KL',
            'V1.07',
            '3451354907A7'
        );
        $this->assertMotherboard(
            'dell-inc.-0xc7mm-a01',
            'Dell Inc.',
            '0XC7MM',
            'A01',
            '7584AD78724E'
        );
        $this->assertMotherboard(
            'intel-corporation-nuc6i7kyb-h90766-402',
            'Intel Corporation',
            'NUC6i7KYB',
            'H90766-402',
            'FCA7A82BFDE5'
        );
        $this->assertMotherboard(
            'fujitsu-fjnb1d5',
            'FUJITSU',
            'FJNB1D5',
            '',
            'BA15B44DC033'
        );
        $this->assertMotherboard(
            'supermicro-x8dtn-2.0',
            'Supermicro',
            'X8DTN',
            '2.0',
            '50F52177DF45'
        );
        $this->assertMotherboard(
            'lenovo-m-stick-sdk0j64923-win+2667031165551',
            'Lenovo',
            'm-Stick',
            'SDK0J64923 WIN+2667031165551',
            '2DD4B730FC08'
        );
    }

    protected function assertMotherboard(string $id, string $manufacturer, string $productName, string $version, string $computerId)
    {
        $motherboard = $this->entityManager
            ->getRepository(Motherboard::class)
            ->find($id)
        ;

        $computer = $this->entityManager
            ->getRepository(Computer::class)
            ->find($computerId)
        ;

        $this->assertInstanceOf(Motherboard::class, $motherboard);
        $this->assertSame($id, $motherboard->id);
        $this->assertSame($manufacturer, $motherboard->manufacturer);
        $this->assertSame($productName, $motherboard->productName);
        $this->assertSame($version, $motherboard->version);
        $this->assertContains($computer, $motherboard->getComputers());
    }
}
