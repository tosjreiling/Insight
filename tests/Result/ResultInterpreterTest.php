<?php

namespace Alternate\Insight\Tests\Result;

use Alternate\Insight\Query\Aggregate;
use Alternate\Insight\Query\QueryIntent;
use Alternate\Insight\Result\ResultInterpreter;

use Arc\Test\Core\TestCase;

final class ResultInterpreterTest extends TestCase {
    public function testAggregateCountIsInterpreted(): void {
        $intent = new QueryIntent(
            table: "beleg",
            aggregates: [
                new Aggregate("count")
            ]
        );

        $rows = [
            ["aggregate_count" => 10]
        ];

        $interpreter = new ResultInterpreter();
        $result = $interpreter->interpret($intent, $rows);

        $this->assertEquals("aggregate", $result["type"]);
        $this->assertEquals(10, $result["value"]);
        $this->assertEquals("count", $result["function"]);
    }

    public function testAggregateSumIsInterpreted(): void {
        $intent = new QueryIntent(
            table: "beleg",
            aggregates: [
                new Aggregate("sum", "bel_istwert")
            ]
        );

        $rows = [
            ["aggregate_sum" => 1500]
        ];

        $interpreter = new ResultInterpreter();
        $result = $interpreter->interpret($intent, $rows);

        $this->assertEquals("aggregate", $result["type"]);
        $this->assertEquals(1500, $result["value"]);
        $this->assertEquals("sum", $result["function"]);
    }

    public function testPlainSelectIsInterpreted(): void {
        $intent = new QueryIntent(
            table: "beleg"
        );

        $rows = [
            ['bel_nr' => 1],
            ['bel_nr' => 2]
        ];

        $interpreter = new ResultInterpreter();
        $result = $interpreter->interpret($intent, $rows);

        $this->assertEquals("list", $result["type"]);
        $this->assertCount(2, $result["rows"]);
    }

    public function testEmptyResult(): void {
        $intent = new QueryIntent(
            table: "beleg"
        );

        $rows = [];

        $interpreter = new ResultInterpreter();
        $result = $interpreter->interpret($intent, $rows);

        $this->assertEquals("empty", $result["type"]);
    }

    public function testGroupedAggregateIsInterpreted(): void {
        $intent = new QueryIntent(
            table: "beleg",
            aggregates: [
                new Aggregate("sum", "bel_istwert")
            ],
            groupBy: ["bel_typ"]
        );

        $rows = [
            ["bel_typ" => 1, "aggregate_sum" => 1000],
            ["bel_typ" => 4, "aggregate_sum" => 5000]
        ];

        $interpreter = new ResultInterpreter();
        $result = $interpreter->interpret($intent, $rows);

        $this->assertEquals("grouped_aggregate", $result["type"]);
        $this->assertEquals(1000, $result["groups"][0]["metrics"]["sum"]);
        $this->assertEquals(5000, $result["groups"][1]["metrics"]["sum"]);
        $this->assertCount(2, $result["groups"]);
    }

    public function testMultipleAggregatesAreInterpreted(): void {
        $intent = new QueryIntent(
            table: "beleg",
            aggregates: [
                new Aggregate("count"),
                new Aggregate("sum", "bel_istwert")
            ]
        );

        $rows = [
            [
                "aggregate_count" => 10,
                "aggregate_sum" => 5000
            ]
        ];

        $interpreter = new ResultInterpreter();
        $result = $interpreter->interpret($intent, $rows);

        $this->assertEquals("multi_aggregate", $result["type"]);
        $this->assertEquals(10, $result["metrics"]["count"]);
        $this->assertEquals(5000, $result["metrics"]["sum"]);
    }

    public function testMultiFieldGroupedAggregateIsInterpreted(): void {
        $intent = new QueryIntent(
            table: "beleg",
            aggregates: [
                new Aggregate("sum", "bel_istwert")
            ],
            groupBy: ["bel_typ", "bel_datum"]
        );

        $rows = [
            [
                "bel_typ" => 1,
                "bel_datum" => 20260101,
                "aggregate_sum" => 1000
            ],
            [
                "bel_typ" => 4,
                "bel_datum" => 20260101,
                "aggregate_sum" => 5000
            ]
        ];

        $interpreter = new ResultInterpreter();
        $result = $interpreter->interpret($intent, $rows);

        $this->assertEquals("grouped_aggregate", $result["type"]);
        $this->assertCount(2, $result["groups"]);
        $this->assertEquals(["bel_typ" => 1, "bel_datum" => 20260101], $result["groups"][0]["group"]);
    }

    public function testGroupedMultipleAggregatesAreInterpreted(): void
    {
        $intent = new QueryIntent(
            table: "beleg",
            aggregates: [
                new Aggregate("count"),
                new Aggregate("sum", "bel_istwert")
            ],
            groupBy: ["bel_typ"]
        );

        $rows = [
            [
                "bel_typ" => 4,
                "aggregate_count" => 10,
                "aggregate_sum" => 5000
            ]
        ];

        $interpreter = new ResultInterpreter();
        $result = $interpreter->interpret($intent, $rows);

        $this->assertEquals("grouped_multi_aggregate", $result["type"]);
        $this->assertEquals(10, $result["groups"][0]["metrics"]["count"]);
        $this->assertEquals(5000, $result["groups"][0]["metrics"]["sum"]);
    }
}