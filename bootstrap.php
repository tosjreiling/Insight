<?php

use Alternate\Insight\Insight;
use Alternate\Insight\Planner\SqlPlanner;
use Alternate\Insight\Query\Aggregate;
use Alternate\Insight\Query\Filter;
use Alternate\Insight\Query\QueryIntent;
use Alternate\Insight\Query\QueryService;
use Alternate\Insight\Query\TimeRange;
use Alternate\Insight\Result\ResultInterpreter;
use Alternate\Insight\Schema\SchemaRegistry;
use Arc\Core\Support\ArrayPrinter;

require __DIR__ . "/vendor/autoload.php";

Insight::boot();

$schemas = new SchemaRegistry(__DIR__ . "/schema");
$planner = new SqlPlanner();
$interpreter = new ResultInterpreter();
$service = new QueryService($schemas, $planner, $interpreter);

echo "=== SCHEMA CHECK ===" . PHP_EOL;
echo $schemas->table("beleg")->column("bel_typ")->description() . PHP_EOL . PHP_EOL;

echo "=== COMPLEX QUERY TEST ===" . PHP_EOL;

$intent = new QueryIntent(
    table: "beleg",
    filters: [
        new Filter("bel_typ", "=", 4)
    ],
    aggregates: [
        new Aggregate("count"),
        new Aggregate("sum", "bel_istwert")
    ],
    timeRange: TimeRange::thisMonth("bel_datum") ,
    groupBy: ["bel_typ", "bel_datum"]
);

$result = $service->run($intent);

echo "=== STRUCTURED RESULT ===" . PHP_EOL;
ArrayPrinter::print($result);

echo "=== SQL PREVIEW ===" . PHP_EOL;
$sql = $planner->plan($intent, $schemas);
echo $sql->toString() . PHP_EOL;