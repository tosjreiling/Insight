<?php

namespace Alternate\Insight\Query;

final class QueryIntent {
    public function __construct(
        private string $table,
        private array $filters = [],
        private array $aggregates = [],
        private ?TimeRange $timeRange = null,
        private array $groupBy = [],
    ) {}

    public function table(): string {
        return $this->table;
    }

    public function filters(): array {
        return $this->filters;
    }

    public function aggregates(): array {
        return $this->aggregates;
    }

    public function timeRange(): ?TimeRange {
        return $this->timeRange;
    }

    public function groupBy(): array {
        return $this->groupBy;
    }
}