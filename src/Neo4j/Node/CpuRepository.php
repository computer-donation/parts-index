<?php

namespace App\Neo4j\Node;

use App\Neo4j\FlushTrait;
use Laudis\Neo4j\Databags\Statement;

class CpuRepository
{
    use FlushTrait;

    public function setUp(): void
    {
        $this->addStatements([
            Statement::create('CREATE CONSTRAINT IF NOT EXISTS FOR (c:Cpu) REQUIRE c.id IS UNIQUE'),
            Statement::create('CREATE FULLTEXT INDEX searchCpu IF NOT EXISTS FOR (c:Cpu) ON EACH [c.vendor, c.model]'),
        ]);
    }

    public function create(string $id, string $vendor, string $model): void
    {
        $this->addStatement(Statement::create(
            'MERGE (cpu:Cpu {id: $id}) ON CREATE SET cpu += {vendor: $vendor, model: $model}',
            ['id' => $id, 'vendor' => $vendor, 'model' => $model]
        ));
    }
}
