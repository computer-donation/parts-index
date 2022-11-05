<?php

namespace App\Neo4j\Relationship;

use App\Neo4j\FlushTrait;
use Laudis\Neo4j\Databags\Statement;

class ProbeCpuRepository
{
    use FlushTrait;

    public function create(string $probeId, string $cpuId): void
    {
        $this->addStatement(Statement::create(
            'MERGE (cpu:Cpu {id: $cpuId}) MERGE (probe:Probe {id: $probeId}) MERGE (probe)-[r:HAS_CPU]->(cpu)',
            ['cpuId' => $cpuId, 'probeId' => $probeId]
        ));
    }
}
