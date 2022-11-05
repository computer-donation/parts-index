<?php

namespace App\Graph\Node;

use App\Graph\GraphTrait;

class ComputerRepository
{
    use GraphTrait;

    public function setUp(): void
    {
        $this->graphHelper->rawQuery('CREATE INDEX ON :Computer(id)');
        // $this->graphHelper->rawQuery('CREATE CONSTRAINT ON (c:Computer) ASSERT c.id IS UNIQUE');
        $this->graphHelper->rawQuery("CALL db.idx.fulltext.createNodeIndex('Computer', 'type', 'vendor', 'model')");
    }

    public function create(string $id, string $type, string $vendor, string $model): void
    {
        $this->graphHelper->rawQuery(
            'MERGE (computer:Computer {id: $id}) ON CREATE SET computer += {type: $type, vendor: $vendor, model: $model}',
            ['id' => $id, 'type' => $type, 'vendor' => $vendor, 'model' => $model]
        );
    }
}
