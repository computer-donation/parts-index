<?php

namespace App\Graph\Relationship;

use App\Graph\GraphTrait;

class ProbeCpuRepository
{
    use GraphTrait;

    public function create(string $probeId, string $cpuId): void
    {
        $this->graphHelper->rawQuery(
            'MERGE (cpu:Cpu {id: $cpuId}) MERGE (probe:Probe {id: $probeId}) MERGE (probe)-[r:HAS_CPU]->(cpu)',
            ['cpuId' => $cpuId, 'probeId' => $probeId]
        );
    }
}
