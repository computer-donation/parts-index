<?php

namespace App\Graph\Node;

use App\Graph\GraphHelper;
use App\Graph\QueryHelper;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Query;

class ComputerRepository
{
    public function __construct(protected QueryHelper $queryHelper, protected GraphHelper $graphHelper)
    {
    }

    public function setUp(): void
    {
        $this->graphHelper->rawQuery('CREATE INDEX ON :Computer(id)');
        // $this->graphHelper->rawQuery('CREATE CONSTRAINT ON (c:Computer) ASSERT c.id IS UNIQUE');
        $this->graphHelper->rawQuery("CALL db.idx.fulltext.createNodeIndex('Computer', 'type', 'vendor', 'model')");
    }

    public function create(string $computerId, string $type, string $vendor, string $model, string $probeId): void
    {
        $computerVar = Query::variable(uniqid('computer_'));
        $probeVar = $this->queryHelper->track('Probe', $probeId);

        $set = new SetClause();
        $set->addAssignment($computerVar->assign(Query::map([
            'type' => Query::literal($type),
            'vendor' => Query::literal($vendor),
            'model' => Query::literal($model),
        ]))->setMutate());

        $computer = Query::node('Computer')
            ->named($computerVar)
            ->withProperty('id', Query::literal($computerId));

        $this->queryHelper
            ->query()
            ->merge($computer, $set)
            ->merge($probeVar->relationshipTo(Query::node()->named($computerVar), 'HAS_COMPUTER'));
    }
}
