<?php

namespace App\Graph\Node;

use App\Graph\GraphTrait;

class MotherboardRepository
{
    use GraphTrait;

    public function setUp(): void
    {
        $this->graphHelper->rawQuery('CREATE INDEX ON :Motherboard(id)');
        // $this->graphHelper->rawQuery('CREATE CONSTRAINT ON (m:Motherboard) ASSERT m.id IS UNIQUE');
        $this->graphHelper->rawQuery("CALL db.idx.fulltext.createNodeIndex('Motherboard', 'manufacturer', 'productName', 'version')");
    }

    public function create(string $id, string $manufacturer, string $productName, string $version): void
    {
        $this->graphHelper->rawQuery(
            'MERGE (motherboard:Motherboard {id: $id}) ON CREATE SET motherboard += {manufacturer: $manufacturer, productName: $productName, version: $version}',
            ['id' => $id, 'manufacturer' => $manufacturer, 'productName' => $productName, 'version' => $version]
        );
    }
}
