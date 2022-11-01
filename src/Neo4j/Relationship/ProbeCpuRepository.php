<?php

namespace App\Neo4j\Relationship;

use Laudis\Neo4j\Contracts\ClientInterface;

class ProbeCpuRepository
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function create(string $probeId, string $cpuId): void
    {
        $this->client->run('MERGE (cpu:Cpu {id: $cpuId}) MERGE (probe:Probe {id: $probeId}) MERGE (probe)-[r:HAS_CPU]->(cpu)', ['cpuId' => $cpuId, 'probeId' => $probeId]);
    }
}
