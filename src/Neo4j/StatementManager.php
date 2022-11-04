<?php

namespace App\Neo4j;

use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Databags\Statement;

class StatementManager
{
    public function __construct(protected ClientInterface $client, protected array $statements = [])
    {
    }

    public function addStatement(Statement $statement): void
    {
        $this->statements[] = $statement;
    }

    public function clear(): void
    {
        $this->statements = [];
    }

    public function flush(): void
    {
        $this->client->runStatements($this->statements);
    }
}
