<?php

namespace App\Tests\Command;

use App\Entity\Motherboard;

class IndexMotherboardCommandTest extends CommandTestCase
{
    protected array $data = [
        [
            'asustek-computer-inc-v241da-1-0',
            'ASUSTeK COMPUTER INC.',
            'V241DA',
            '1.0',
            '3FEBF400F4CB',
        ],
        [
            'kbl-buzz-kl-v1-07',
            'KBL',
            'Buzz_KL',
            'V1.07',
            '3451354907A7',
        ],
        [
            'dell-inc-0xc7mm-a01',
            'Dell Inc.',
            '0XC7MM',
            'A01',
            '7584AD78724E',
        ],
        [
            'intel-corporation-nuc6i7kyb-h90766-402',
            'Intel Corporation',
            'NUC6i7KYB',
            'H90766-402',
            'FCA7A82BFDE5',
        ],
        [
            'fujitsu-fjnb1d5',
            'FUJITSU',
            'FJNB1D5',
            '',
            'BA15B44DC033',
        ],
        [
            'supermicro-x8dtn-2-0',
            'Supermicro',
            'X8DTN',
            '2.0',
            '50F52177DF45',
        ],
        [
            'lenovo-m-stick-sdk0j64923-win-2667031165551',
            'Lenovo',
            'm-Stick',
            'SDK0J64923 WIN+2667031165551',
            '2DD4B730FC08',
        ],
    ];

    protected function getCommand(): string
    {
        return 'app:index-motherboard';
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
        foreach ($this->data as $motherboard) {
            $this->assertMotherboard(...$motherboard);
        }
    }

    protected function assertMotherboard(string $id, string $manufacturer, string $productName, string $version)
    {
        $motherboard = $this->entityManager
            ->getRepository(Motherboard::class)
            ->find($id)
        ;

        $this->assertInstanceOf(Motherboard::class, $motherboard);
        $this->assertSame($id, $motherboard->id);
        $this->assertSame($manufacturer, $motherboard->manufacturer);
        $this->assertSame($productName, $motherboard->productName);
        $this->assertSame($version, $motherboard->version);
    }

    protected function assertNodes(): void
    {
        foreach ($this->data as $motherboard) {
            $this->assertMotherboardNode(...$motherboard);
        }
    }

    protected function assertMotherboardNode(string $id, string $manufacturer, string $productName, string $version): void
    {
        $result = $this->client->run('MATCH (motherboard:Motherboard {id: $id}) RETURN motherboard', ['id' => $id])->first();
        $motherboard = $result->get('motherboard');
        $this->assertSame($manufacturer, $motherboard->getProperty('manufacturer'));
        $this->assertSame($productName, $motherboard->getProperty('productName'));
        $this->assertSame($version, $motherboard->getProperty('version'));
    }

    protected function assertRelationships(): void
    {
        foreach ($this->data as $motherboard) {
            $this->assertComputerMotherboardRelationship(...$motherboard);
        }
    }

    protected function assertComputerMotherboardRelationship(): void
    {
        $args = func_get_args();
        $motherboardId = reset($args);
        $computerId = end($args);
        $result = $this->client->run('MATCH (computer:Computer {id: $computerId}) MATCH (motherboard:Motherboard {id: $motherboardId}) RETURN exists((computer)-[:HAS_MOTHERBOARD]->(motherboard)) as hasRelationship', ['computerId' => $computerId, 'motherboardId' => $motherboardId])->first();
        $this->assertTrue($result->get('hasRelationship'));
    }
}
