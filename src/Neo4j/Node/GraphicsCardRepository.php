<?php

namespace App\Neo4j\Node;

use App\Neo4j\FlushTrait;
use Laudis\Neo4j\Databags\Statement;

class GraphicsCardRepository
{
    use FlushTrait;

    public function setUp(): void
    {
        $this->addStatements([
            Statement::create('CREATE INDEX ON :GraphicsCard(id)'),
            Statement::create('CREATE CONSTRAINT ON (g:GraphicsCard) ASSERT g.id IS UNIQUE'),
            //Statement::create('CREATE FULLTEXT INDEX searchGraphicsCard IF NOT EXISTS FOR (g:GraphicsCard) ON EACH [g.vendor, g.subVendor, g.device]'),
        ]);
    }

    public function create(string $id, string $vendor, ?string $subVendor, string $device): void
    {
        $this->addStatement(Statement::create(
            'MERGE (gpu:GraphicsCard {id: $id}) ON CREATE SET gpu += {vendor: $vendor, subVendor: $subVendor, device: $device}',
            ['id' => $id, 'vendor' => $vendor, 'subVendor' => $subVendor, 'device' => $device]
        ));
    }
}
