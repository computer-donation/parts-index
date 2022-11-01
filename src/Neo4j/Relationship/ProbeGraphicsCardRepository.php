<?php

namespace App\Neo4j\Relationship;

use Laudis\Neo4j\Contracts\ClientInterface;

class ProbeGraphicsCardRepository
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function create(string $probeId, string $gpuId): void
    {
        $this->client->run('MERGE (gpu:GraphicsCard {id: $gpuId}) MERGE (probe:Probe {id: $probeId}) MERGE (probe)-[r:HAS_GPU]->(gpu)', ['gpuId' => $gpuId, 'probeId' => $probeId]);
    }
}
