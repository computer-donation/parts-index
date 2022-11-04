<?php

namespace App\Neo4j\Relationship;

use App\Neo4j\FlushTrait;
use Laudis\Neo4j\Databags\Statement;

class ProbeGraphicsCardRepository
{
    use FlushTrait;

    public function create(string $probeId, string $gpuId): void
    {
        $this->addStatement(Statement::create(
            'MERGE (gpu:GraphicsCard {id: $gpuId}) MERGE (probe:Probe {id: $probeId}) MERGE (probe)-[r:HAS_GPU]->(gpu)',
            ['gpuId' => $gpuId, 'probeId' => $probeId]
        ));
    }
}
