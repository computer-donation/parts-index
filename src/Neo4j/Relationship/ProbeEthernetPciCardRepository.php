<?php

namespace App\Neo4j\Relationship;

use Laudis\Neo4j\Contracts\ClientInterface;

class ProbeEthernetPciCardRepository
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function create(string $probeId, string $ethernetId): void
    {
        $this->client->run('MERGE (ethernet:EthernetPciCard {id: $ethernetId}) MERGE (probe:Probe {id: $probeId}) MERGE (probe)-[r:HAS_ETHERNET_PCI]->(ethernet)', ['ethernetId' => $ethernetId, 'probeId' => $probeId]);
    }
}
