<?php

namespace App\Graph\Node;

use App\Graph\GraphHelper;
use App\Graph\QueryHelper;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Query;

class MotherboardRepository
{
    public function __construct(protected QueryHelper $queryHelper, protected GraphHelper $graphHelper)
    {
    }

    public function setUp(): void
    {
        $this->graphHelper->rawQuery('CREATE INDEX ON :Motherboard(id)');
        // $this->graphHelper->rawQuery('CREATE CONSTRAINT ON (m:Motherboard) ASSERT m.id IS UNIQUE');
        $this->graphHelper->rawQuery("CALL db.idx.fulltext.createNodeIndex('Motherboard', 'manufacturer', 'productName', 'version')");
    }

    public function create(string $motherboardId, string $manufacturer, string $productName, string $version, string $computerId): void
    {
        $motherboardVar = Query::variable(uniqid('motherboard_'));
        $computerVar = $this->queryHelper->track('Computer', $computerId);

        $set = new SetClause();
        $set->addAssignment($motherboardVar->assign(Query::map([
            'manufacturer' => Query::literal($manufacturer),
            'productName' => Query::literal($productName),
            'version' => Query::literal($version),
        ]))->setMutate());

        $motherboard = Query::node('Motherboard')
            ->named($motherboardVar)
            ->withProperty('id', Query::literal($motherboardId));

        $this->queryHelper
            ->query()
            ->merge($motherboard, $set)
            ->merge($computerVar->relationshipTo(Query::node()->named($motherboardVar), 'HAS_MOTHERBOARD'));
    }
}
