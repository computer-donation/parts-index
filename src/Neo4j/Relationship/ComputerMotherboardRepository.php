<?php

namespace App\Neo4j\Relationship;

use App\Neo4j\FlushTrait;
use Laudis\Neo4j\Databags\Statement;

class ComputerMotherboardRepository
{
    use FlushTrait;

    public function create(string $computerId, string $motherboardId): void
    {
        $this->addStatement(Statement::create(
            'MERGE (computer:Computer {id: $computerId}) MERGE (motherboard:Motherboard {id: $motherboardId}) MERGE (computer)-[r:HAS_MOTHERBOARD]->(motherboard)',
            ['computerId' => $computerId, 'motherboardId' => $motherboardId]
        ));
    }
}
