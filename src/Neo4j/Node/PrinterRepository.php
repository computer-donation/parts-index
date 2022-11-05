<?php

namespace App\Neo4j\Node;

use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Databags\Statement;

class PrinterRepository
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function setUp(): void
    {
        $this->client->runStatements([
            Statement::create('CREATE CONSTRAINT IF NOT EXISTS FOR (p:Printer) REQUIRE p.id IS UNIQUE'),
            Statement::create('CREATE FULLTEXT INDEX searchPrinter IF NOT EXISTS FOR (p:Printer) ON EACH [p.vendor, p.device]'),
        ]);
    }

    public function create(string $id, string $vendor, string $device): void
    {
        $this->client->run('MERGE (printer:Printer {id: $id}) ON CREATE SET printer += {vendor: $vendor, device: $device}', ['id' => $id, 'vendor' => $vendor, 'device' => $device]);
    }
}
