<?php

namespace App\Graph\Relationship;

use App\Graph\GraphTrait;

class ProbeComputerRepository
{
    use GraphTrait;

    public function create(string $probeId, string $computerId): void
    {
        $this->graphHelper->rawQuery(
            'MERGE (computer:Computer {id: $computerId}) MERGE (probe:Probe {id: $probeId}) MERGE (probe)-[r:HAS_COMPUTER]->(computer)',
            ['computerId' => $computerId, 'probeId' => $probeId]
        );
    }
}
