<?php

namespace Alternate\Insight\Tests\Query;

use Alternate\Insight\Insight;
use Alternate\Insight\Planner\SqlPlanner;
use Alternate\Insight\Query\Aggregate;
use Alternate\Insight\Query\QueryIntent;
use Alternate\Insight\Query\QueryService;
use Alternate\Insight\Result\ResultInterpreter;
use Alternate\Insight\Schema\SchemaRegistry;
use Arc\Test\Core\TestCase;

final class QueryServiceTest extends TestCase {
    public static function beforeAll(): void {
        Insight::boot();
    }

    public function testCountQueryReturnsArray(): void {
        $schemas = new SchemaRegistry(__DIR__ . "/../../schema");
        $planner = new SqlPlanner();
        $interpreter = new ResultInterpreter();
        $service = new QueryService($schemas, $planner, $interpreter);

        $intent = new QueryIntent(
            table: "beleg",
            aggregates: [new Aggregate("count")]
        );

        $result = $service->run($intent);
        $this->assertIsArray($result);
    }
}