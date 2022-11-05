<?php

namespace App\Neo4j\Node;

use App\Neo4j\FlushTrait;
use Laudis\Neo4j\Databags\Statement;

class ComputerRepository
{
    use FlushTrait;

    public function setUp(): void
    {
        $this->addStatements([
            Statement::create('CREATE CONSTRAINT IF NOT EXISTS FOR (c:Computer) REQUIRE c.id IS UNIQUE'),
            Statement::create('CREATE FULLTEXT INDEX searchComputer IF NOT EXISTS FOR (c:Computer) ON EACH [c.type, c.vendor, c.model]'),
        ]);
    }

    public function create(string $id, string $type, string $vendor, string $model): void
    {
        $this->addStatement(Statement::create(
            'MERGE (computer:Computer {id: $id}) ON CREATE SET computer += {type: $type, vendor: $vendor, model: $model}',
            ['id' => $id, 'type' => $type, 'vendor' => $vendor, 'model' => $model]
        ));
    }
}
