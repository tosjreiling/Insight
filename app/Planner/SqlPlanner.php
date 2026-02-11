<?php

namespace Alternate\Insight\Planner;

use Alternate\Insight\Query\Aggregate;
use Alternate\Insight\Query\Filter;
use Alternate\Insight\Query\QueryIntent;
use Alternate\Insight\Schema\SchemaRegistry;

use Alternate\Insight\Schema\TableDefinition;
use Arc\Base\Base;
use Arc\Base\Core\Builders\SelectBuilder;
use Arc\Base\Core\Expression;

use Arc\Guard\Guard;
use Arc\Guard\Rules\Logic\InRule;
use InvalidArgumentException;
use Throwable;

final class SqlPlanner {
    public function plan(QueryIntent $intent, SchemaRegistry $schemas): SelectBuilder {
        $table = $schemas->table($intent->table());

        $builder = Base::instance()
            ->select()
            ->from($table->name());

        foreach($intent->filters() as $filter) {
            /** @var Filter $filter */
            $this->assertColumnExists($filter->field(), $table);

            Guard::instance()
                ->data(["operator" => $filter->operator()])
                ->for("operator")
                ->rule(InRule::key(), ["=", "<>", ">", "<", ">=", "<="])
                ->and()
                ->validateOrFail();

            $builder->where(
                $filter->field(),
                $filter->operator(),
                $filter->value()
            );
        }

        if($intent->timeRange()) {
            $range = $intent->timeRange();

            $builder->between(
                $range->field(),
                $range->from(),
                $range->to()
            );
        }

        foreach($intent->aggregates() as $aggregate) {
            /** @var Aggregate $aggregate */
            $expr = match(strtolower($aggregate->function())) {
                "count" => Expression::count($aggregate->field()),
                "sum" => Expression::sum($aggregate->field()),
                "avg" => Expression::average($aggregate->field()),
                "min" => Expression::min($aggregate->field()),
                "max" => Expression::max($aggregate->field()),
                default => throw new InvalidArgumentException("Unsupported aggregate function: {$aggregate->function()}")
            };

            $alias = $aggregate->alias() ?? "aggregate_" . strtolower($aggregate->function());
            $expr->as($alias);

            $aggregateExpressions[] = $expr;

            $selectColumns = [];

            // Add groupBy fields first
            foreach ($intent->groupBy() as $groupField) {
                $this->assertColumnExists($groupField, $table);
                $selectColumns[] = $groupField;
            }

            // Add aggregates
            foreach ($aggregateExpressions as $expr) {
                $selectColumns[] = $expr;
            }

            if (!empty($selectColumns)) {
                $builder->columns($selectColumns);
            }
        }

        foreach($intent->groupBy() as $column) {
            $builder->groupBy($column);
        }

        return $builder;
    }

    private function assertColumnExists(string $column, TableDefinition $table): void {
        try {
            $table->column($column);
        } catch (Throwable $e) {
            throw new InvalidArgumentException("Column [{$column}] does not exist in table [{$table->name()}].");
        }
    }
}