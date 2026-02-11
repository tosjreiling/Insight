<?php

namespace Alternate\Insight\Query;

final class TimeRange {
    private function __construct(
        private string $field,
        private int $from,
        private int $to
    ) {}

    public static function between(string $field, int $from, int $to): self {
        return new self($field, $from, $to);
    }

    public static function thisMonth(string $field): self {
        $from = (int) date("Ym01");
        $to = (int) date("Ymt");

        return new self($field, $from, $to);
    }

    public function field(): string {
        return $this->field;
    }

    public function from(): int {
        return $this->from;
    }

    public function to(): int {
        return $this->to;
    }
}