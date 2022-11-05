<?php

namespace App\Graph\Node;

use App\Graph\GraphTrait;

class GraphicsCardRepository
{
    use GraphTrait;

    public function setUp(): void
    {
        $this->graphHelper->rawQuery('CREATE INDEX ON :GraphicsCard(id)');
        // $this->graphHelper->rawQuery('CREATE CONSTRAINT ON (g:GraphicsCard) ASSERT g.id IS UNIQUE');
        $this->graphHelper->rawQuery("CALL db.idx.fulltext.createNodeIndex('GraphicsCard', 'vendor', 'subVendor', 'device')");
    }

    public function create(string $id, string $vendor, ?string $subVendor, string $device): void
    {
        $this->graphHelper->rawQuery(
            'MERGE (gpu:GraphicsCard {id: $id}) ON CREATE SET gpu += {vendor: $vendor, subVendor: $subVendor, device: $device}',
            ['id' => $id, 'vendor' => $vendor, 'subVendor' => $subVendor, 'device' => $device]
        );
    }
}
