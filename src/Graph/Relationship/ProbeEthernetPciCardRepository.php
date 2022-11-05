<?php

namespace App\Graph\Relationship;

use App\Graph\GraphTrait;

class ProbeEthernetPciCardRepository
{
    use GraphTrait;

    public function create(string $probeId, string $ethernetId): void
    {
        $this->graphHelper->rawQuery(
            'MERGE (ethernet:EthernetPciCard {id: $ethernetId}) MERGE (probe:Probe {id: $probeId}) MERGE (probe)-[r:HAS_ETHERNET_PCI]->(ethernet)',
            ['ethernetId' => $ethernetId, 'probeId' => $probeId]
        );
    }
}
