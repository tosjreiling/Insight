<?php

namespace Alternate\Insight\Tests\Planner;

use Alternate\Insight\Insight;
use Alternate\Insight\Planner\SqlPlanner;
use Alternate\Insight\Query\Filter;
use Alternate\Insight\Query\QueryIntent;
use Alternate\Insight\Schema\SchemaRegistry;

use Arc\Test\Core\TestCase;

use InvalidArgumentException;

final class SqlPlannerTest extends TestCase {
    private SchemaRegistry $schemas;

    public static function beforeAll(): void {
        Insight::boot();
    }

    public function beforeEach(): void {
        $this->schemas = new SchemaRegistry(__DIR__ . "/../../schema");
    }

    public function testInvalidColumnThrows(): void {
        $this->expectException(InvalidArgumentException::class);

        $intent = new QueryIntent(
            table: "beleg",
            filters: [
                new Filter("non_existent_column", "=", 4)
            ]
        );

        $planner = new SqlPlanner();
        $planner->plan($intent, $this->schemas);
    }

    public function testValidFilterBuildsSelectBuilder(): void {
        $intent = new QueryIntent(
            table: "beleg",
            filters: [
                new Filter("bel_typ", "=", 4)
            ]
        );

        $planner = new SqlPlanner();
        $builder = $planner->plan($intent, $this->schemas);

        $sql = $builder->toString();

        $expectedSql = "SELECT * FROM beleg WHERE bel_typ = 4";

        $this->assertEquals($expectedSql, $sql);
        $this->assertNotNull($builder);
    }
}