<?php

namespace Alternate\Insight\Query;

use Alternate\Insight\Planner\SqlPlanner;
use Alternate\Insight\Result\ResultInterpreter;
use Alternate\Insight\Schema\SchemaRegistry;

final class QueryService {
    private SchemaRegistry $schemas;
    private SQLPlanner $planner;
    private ResultInterpreter $interpreter;

    public function __construct(SchemaRegistry $schemas, SqlPlanner $planner, ResultInterpreter $interpreter) {
        $this->schemas = $schemas;
        $this->planner = $planner;
        $this->interpreter = $interpreter;
    }

    public function run(QueryIntent $intent): array {
        $builder = $this->planner->plan($intent, $this->schemas);
        $rows = $builder->get();

        return $this->interpreter->interpret($intent, $rows);
    }
}