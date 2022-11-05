<?php

namespace App\Neo4j\Relationship;

use Laudis\Neo4j\Contracts\ClientInterface;

class ProbeComputerRepository
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function create(string $probeId, string $computerId): void
    {
        $this->client->run('MERGE (computer:Computer {id: $computerId}) MERGE (probe:Probe {id: $probeId}) MERGE (probe)-[r:HAS_COMPUTER]->(computer)', ['computerId' => $computerId, 'probeId' => $probeId]);
    }
}
