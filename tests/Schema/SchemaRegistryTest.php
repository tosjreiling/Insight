<?php

namespace Alternate\Insight\Tests\Schema;

use Alternate\Insight\Schema\SchemaRegistry;

use Arc\Test\Core\TestCase;

use InvalidArgumentException;

final class SchemaRegistryTest extends TestCase {
    public function testBelegSchemaLoads(): void {
        $schemas = new SchemaRegistry(__DIR__ . "/../../schema");
        $table = $schemas->table("beleg");

        $this->assertEquals("beleg", $table->name());
    }

    public function testColumnExists(): void {
        $schemas = new SchemaRegistry(__DIR__ . "/../../schema");
        $table = $schemas->table("beleg");
        $column = $table->column("bel_typ");

        $this->assertEquals("bel_typ", $column->name());
        $this->assertEquals("enum", $column->type());
    }

    public function testInvalidColumnThrows(): void {
        $this->expectException(InvalidArgumentException::class);

        $schemas = new SchemaRegistry(__DIR__ . "/../../schema");
        $table = $schemas->table("beleg");
        $table->column("non_existent_column");
    }
}