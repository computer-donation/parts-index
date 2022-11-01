<?php

namespace App\Neo4j\Node;

use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Databags\Statement;

class MotherboardRepository
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function setUp(): void
    {
        $this->client->runStatements([
            Statement::create('CREATE CONSTRAINT IF NOT EXISTS FOR (m:Motherboard) REQUIRE m.id IS UNIQUE'),
            Statement::create('CREATE FULLTEXT INDEX searchMotherboard IF NOT EXISTS FOR (m:Motherboard) ON EACH [m.manufacturer, m.productName, m.version]'),
        ]);
    }

    public function create(string $id, string $manufacturer, string $productName, string $version): void
    {
        $this->client->run('MERGE (motherboard:Motherboard {id: $id}) ON CREATE SET motherboard += {manufacturer: $manufacturer, productName: $productName, version: $version}', ['id' => $id, 'manufacturer' => $manufacturer, 'productName' => $productName, 'version' => $version]);
    }
}
