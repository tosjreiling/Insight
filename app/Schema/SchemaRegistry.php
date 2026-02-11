<?php

namespace Alternate\Insight\Schema;

use InvalidArgumentException;

final class SchemaRegistry {
    private array $tables = [];

    public function __construct(string $schema) {
        foreach(glob($schema . "/*.php") as $file) {
            $this->load($file);
        }
    }

    public function table(string $name): TableDefinition {
        if(!isset($this->tables[$name])) {
            throw new InvalidArgumentException("Table [{$name}] is not defined in the schema.");
        }

        return $this->tables[$name];
    }

    private function load(string $file): void {
        $definition = require $file;

        if(!$definition instanceof TableDefinition)
            throw new InvalidArgumentException("Schema file [{$file}] must return an instance of TableDefinition.");

        $this->tables[$definition->name()] = $definition;
    }
}