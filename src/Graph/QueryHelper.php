<?php

namespace App\Graph;

use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Variable;

class QueryHelper
{
    protected ?Query $query = null;
    protected array $trackedNodes = [];

    public function track(string $label, string $id): Variable
    {
        if (!isset($this->trackedNodes[$label][$id])) {
            $variable = Query::variable('node_'.$id);
            $node = Query::node($label)
                ->named($variable)
                ->withProperty('id', Query::literal($id));
            $this->query()->merge($node);
            $this->trackedNodes[$label][$id] = $variable;
        }

        return $this->trackedNodes[$label][$id];
    }

    public function query(): Query
    {
        if (!$this->query) {
            $this->query = Query::new();
        }

        return $this->query;
    }

    public function build(): string
    {
        $queryString = $this->query->build();
        $this->query = null;
        $this->trackedNodes = [];

        return $queryString;
    }
}
