<?php

namespace App\Neo4j\Node;

use App\Neo4j\FlushTrait;
use Laudis\Neo4j\Databags\Statement;

class EthernetPciCardRepository
{
    use FlushTrait;

    public function setUp(): void
    {
        $this->addStatements([
            Statement::create('CREATE CONSTRAINT IF NOT EXISTS FOR (e:EthernetPciCard) REQUIRE e.id IS UNIQUE'),
            Statement::create('CREATE FULLTEXT INDEX searchEthernetPciCard IF NOT EXISTS FOR (e:EthernetPciCard) ON EACH [e.vendor, e.subVendor, e.device]'),
        ]);
    }

    public function create(string $id, string $vendor, ?string $subVendor, string $device): void
    {
        $this->addStatement(Statement::create(
            'MERGE (ethernet:EthernetPciCard {id: $id}) ON CREATE SET ethernet += {vendor: $vendor, subVendor: $subVendor, device: $device}',
            ['id' => $id, 'vendor' => $vendor, 'subVendor' => $subVendor, 'device' => $device]
        ));
    }
}
