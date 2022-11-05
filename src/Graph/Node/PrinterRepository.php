<?php

namespace App\Graph\Node;

use App\Graph\GraphTrait;

class PrinterRepository
{
    use GraphTrait;

    public function setUp(): void
    {
        $this->graphHelper->rawQuery('CREATE INDEX ON :Printer(id)');
        // $this->graphHelper->rawQuery('CREATE CONSTRAINT ON (p:Printer) ASSERT p.id IS UNIQUE');
        $this->graphHelper->rawQuery("CALL db.idx.fulltext.createNodeIndex('Printer', 'vendor', 'device')");
    }

    public function create(string $id, string $vendor, string $device): void
    {
        $this->graphHelper->rawQuery(
            'MERGE (printer:Printer {id: $id}) ON CREATE SET printer += {vendor: $vendor, device: $device}',
            ['id' => $id, 'vendor' => $vendor, 'device' => $device]
        );
    }
}
