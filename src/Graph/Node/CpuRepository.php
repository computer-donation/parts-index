<?php

namespace App\Graph\Node;

use App\Graph\GraphTrait;

class CpuRepository
{
    use GraphTrait;

    public function setUp(): void
    {
        $this->graphHelper->rawQuery('CREATE INDEX ON :Cpu(id)');
        // $this->graphHelper->rawQuery('CREATE CONSTRAINT ON (c:Cpu) ASSERT c.id IS UNIQUE');
        $this->graphHelper->rawQuery("CALL db.idx.fulltext.createNodeIndex('Cpu', 'vendor', 'model')");
    }

    public function create(string $id, string $vendor, string $model): void
    {
        $this->graphHelper->rawQuery(
            'MERGE (cpu:Cpu {id: $id}) ON CREATE SET cpu += {vendor: $vendor, model: $model}',
            ['id' => $id, 'vendor' => $vendor, 'model' => $model]
        );
    }
}
