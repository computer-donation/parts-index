<?php

namespace App\Graph\Relationship;

use App\Graph\GraphTrait;

class ComputerMotherboardRepository
{
    use GraphTrait;

    public function create(string $computerId, string $motherboardId): void
    {
        $this->graphHelper->rawQuery(
            'MERGE (computer:Computer {id: $computerId}) MERGE (motherboard:Motherboard {id: $motherboardId}) MERGE (computer)-[r:HAS_MOTHERBOARD]->(motherboard)',
            ['computerId' => $computerId, 'motherboardId' => $motherboardId]
        );
    }
}
