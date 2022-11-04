<?php

namespace App\Neo4j;

use Laudis\Neo4j\Databags\Statement;

trait FlushTrait
{
    public function __construct(protected StatementManager $statementManager)
    {
    }

    public function flush(): void
    {
        $this->statementManager->flush();
        $this->statementManager->clear();
    }

    protected function addStatement(Statement $statement): void
    {
        $this->statementManager->addStatement($statement);
    }

    protected function addStatements(array $statements): void
    {
        array_map(fn (Statement $statement) => $this->addStatement($statement), $statements);
    }
}
