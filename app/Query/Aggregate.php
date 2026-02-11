<?php

namespace Alternate\Insight\Query;

final class Aggregate {
    public function __construct(
        private string $function,
        private string $field = "*",
        private ?string $alias = null
    ) {}

    public function function(): string {
        return $this->function;
    }

    public function field(): string {
        return $this->field;
    }

    public function alias(): ?string {
        return $this->alias;
    }
}