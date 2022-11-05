<?php

namespace App\Graph\Node;

use App\Graph\GraphHelper;
use App\Graph\QueryHelper;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Query;

class PrinterRepository
{
    public function __construct(protected QueryHelper $queryHelper, protected GraphHelper $graphHelper)
    {
    }

    public function setUp(): void
    {
        $this->graphHelper->rawQuery('CREATE INDEX ON :Printer(id)');
        // $this->graphHelper->rawQuery('CREATE CONSTRAINT ON (p:Printer) ASSERT p.id IS UNIQUE');
        $this->graphHelper->rawQuery("CALL db.idx.fulltext.createNodeIndex('Printer', 'vendor', 'device')");
    }

    public function create(string $id, string $vendor, string $device): void
    {
        $printerVar = Query::variable(uniqid('printer_'));

        $set = new SetClause();
        $set->addAssignment($printerVar->assign(Query::map([
            'vendor' => Query::literal($vendor),
            'device' => Query::literal($device),
        ]))->setMutate());

        $printer = Query::node('Printer')
            ->named($printerVar)
            ->withProperty('id', Query::literal($id));

        $this->queryHelper
            ->query()
            ->merge($printer, $set);
    }
}
