<?php

namespace App\Graph\Node;

use App\Graph\GraphTrait;

class EthernetPciCardRepository
{
    use GraphTrait;

    public function setUp(): void
    {
        $this->graphHelper->rawQuery('CREATE INDEX ON :EthernetPciCard(id)');
        // $this->graphHelper->rawQuery('CREATE CONSTRAINT ON (e:EthernetPciCard) ASSERT e.id IS UNIQUE');
        $this->graphHelper->rawQuery("CALL db.idx.fulltext.createNodeIndex('EthernetPciCard', 'vendor', 'subVendor', 'device')");
    }

    public function create(string $id, string $vendor, ?string $subVendor, string $device): void
    {
        $this->graphHelper->rawQuery(
            'MERGE (ethernet:EthernetPciCard {id: $id}) ON CREATE SET ethernet += {vendor: $vendor, subVendor: $subVendor, device: $device}',
            ['id' => $id, 'vendor' => $vendor, 'subVendor' => $subVendor, 'device' => $device]
        );
    }
}
