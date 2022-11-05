<?php

namespace App\Graph\Relationship;

use App\Graph\GraphTrait;

class ProbeGraphicsCardRepository
{
    use GraphTrait;

    public function create(string $probeId, string $gpuId): void
    {
        $this->graphHelper->rawQuery(
            'MERGE (gpu:GraphicsCard {id: $gpuId}) MERGE (probe:Probe {id: $probeId}) MERGE (probe)-[r:HAS_GPU]->(gpu)',
            ['gpuId' => $gpuId, 'probeId' => $probeId]
        );
    }
}
