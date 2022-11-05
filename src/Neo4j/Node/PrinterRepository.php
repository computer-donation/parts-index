<?php

namespace App\Neo4j\Node;

use App\Neo4j\FlushTrait;
use Laudis\Neo4j\Databags\Statement;

class PrinterRepository
{
    use FlushTrait;

    public function setUp(): void
    {
        $this->addStatements([
            Statement::create('CREATE INDEX ON :Printer(id)'),
            Statement::create('CREATE CONSTRAINT ON (p:Printer) ASSERT p.id IS UNIQUE'),
            //Statement::create('CREATE FULLTEXT INDEX searchPrinter IF NOT EXISTS FOR (p:Printer) ON EACH [p.vendor, p.device]'),
        ]);
    }

    public function create(string $id, string $vendor, string $device): void
    {
        $this->addStatement(Statement::create(
            'MERGE (printer:Printer {id: $id}) ON CREATE SET printer += {vendor: $vendor, device: $device}',
            ['id' => $id, 'vendor' => $vendor, 'device' => $device]
        ));
    }
}
