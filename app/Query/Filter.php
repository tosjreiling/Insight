<?php

namespace Alternate\Insight\Query;

final class Filter {
    public function __construct(
        private string $field,
        private string $operator,
        private mixed $value
    ) {}

    public function field(): string {
        return $this->field;
    }

    public function operator(): string {
        return $this->operator;
    }

    public function value(): mixed {
        return $this->value;
    }
}