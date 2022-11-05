<?php

namespace App\Neo4j\Node;

use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Databags\Statement;

class GraphicsCardRepository
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function setUp(): void
    {
        $this->client->runStatements([
            Statement::create('CREATE CONSTRAINT IF NOT EXISTS FOR (g:GraphicsCard) REQUIRE g.id IS UNIQUE'),
            Statement::create('CREATE FULLTEXT INDEX searchGraphicsCard IF NOT EXISTS FOR (g:GraphicsCard) ON EACH [g.vendor, g.subVendor, g.device]'),
        ]);
    }

    public function create(string $id, string $vendor, ?string $subVendor, string $device): void
    {
        $this->client->run('MERGE (gpu:GraphicsCard {id: $id}) ON CREATE SET gpu += {vendor: $vendor, subVendor: $subVendor, device: $device}', ['id' => $id, 'vendor' => $vendor, 'subVendor' => $subVendor, 'device' => $device]);
    }
}
