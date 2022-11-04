<?php

namespace App\Neo4j\Relationship;

use App\Neo4j\FlushTrait;
use Laudis\Neo4j\Databags\Statement;

class ProbeComputerRepository
{
    use FlushTrait;

    public function create(string $probeId, string $computerId): void
    {
        $this->addStatement(Statement::create(
            'MERGE (computer:Computer {id: $computerId}) MERGE (probe:Probe {id: $probeId}) MERGE (probe)-[r:HAS_COMPUTER]->(computer)',
            ['computerId' => $computerId, 'probeId' => $probeId]
        ));
    }
}
