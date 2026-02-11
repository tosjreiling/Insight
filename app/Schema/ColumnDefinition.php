<?php

namespace Alternate\Insight\Schema;

final class ColumnDefinition {
    private string $name;
    private string $description;
    private string $type;
    private array $values;

    public function __construct(string $name, string $description, string $type, array $values = []) {
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->values = $values;
    }

    public function name(): string {
        return $this->name;
    }

    public function description(): string {
        return $this->description;
    }

    public function type(): string {
        return $this->type;
    }

    public function values(): array {
        return $this->values;
    }
}