<?php

namespace Alternate\Insight\Schema;

use InvalidArgumentException;

final class TableDefinition {
    private string $name;
    private string $description;
    private string $primaryKey;
    private array $columns;

    public function __construct(string $name, string $description, string $primaryKey, array $columns) {
        $this->name = $name;
        $this->description = $description;
        $this->primaryKey = $primaryKey;
        $this->columns = $columns;
    }

    public function name(): string {
        return $this->name;
    }

    public function column(string $name): ColumnDefinition {
        foreach($this->columns as $column) {
            if($column->name() === $name) {
                return $column;
            }
        }

        throw new InvalidArgumentException("Column [{$name}] does not exist in table [{$this->name}].");
    }

    public function columns(): array {
        return $this->columns;
    }
}