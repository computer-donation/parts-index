<?php

namespace App\Neo4j\Node;

use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Databags\Statement;

class ComputerRepository
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function setUp(): void
    {
        $this->client->runStatements([
            Statement::create('CREATE CONSTRAINT IF NOT EXISTS FOR (c:Computer) REQUIRE c.id IS UNIQUE'),
            Statement::create('CREATE FULLTEXT INDEX searchComputer IF NOT EXISTS FOR (c:Computer) ON EACH [c.type, c.vendor, c.model]'),
        ]);
    }

    public function create(string $id, string $type, string $vendor, string $model): void
    {
        $this->client->run('MERGE (computer:Computer {id: $id}) ON CREATE SET computer += {type: $type, vendor: $vendor, model: $model}', ['id' => $id, 'type' => $type, 'vendor' => $vendor, 'model' => $model]);
    }
}
