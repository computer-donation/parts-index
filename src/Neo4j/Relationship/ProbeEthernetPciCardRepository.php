<?php

namespace App\Neo4j\Relationship;

use App\Neo4j\FlushTrait;
use Laudis\Neo4j\Databags\Statement;

class ProbeEthernetPciCardRepository
{
    use FlushTrait;

    public function create(string $probeId, string $ethernetId): void
    {
        $this->addStatement(Statement::create(
            'MERGE (ethernet:EthernetPciCard {id: $ethernetId}) MERGE (probe:Probe {id: $probeId}) MERGE (probe)-[r:HAS_ETHERNET_PCI]->(ethernet)',
            ['ethernetId' => $ethernetId, 'probeId' => $probeId]
        ));
    }
}
