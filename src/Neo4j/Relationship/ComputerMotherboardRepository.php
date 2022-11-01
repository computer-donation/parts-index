<?php

namespace App\Neo4j\Relationship;

use Laudis\Neo4j\Contracts\ClientInterface;

class ComputerMotherboardRepository
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public function create(string $computerId, string $motherboardId): void
    {
        $this->client->run('MERGE (computer:Computer {id: $computerId}) MERGE (motherboard:Motherboard {id: $motherboardId}) MERGE (computer)-[r:HAS_MOTHERBOARD]->(motherboard)', ['computerId' => $computerId, 'motherboardId' => $motherboardId]);
    }
}
