<?php

namespace Alternate\Insight\Result;

use Alternate\Insight\Query\QueryIntent;

final class ResultInterpreter {
    public function interpret(QueryIntent $intent, array $rows): array {
        if(empty($rows))
            return ["type" => "empty"];

        $hasAggregates = !empty($intent->aggregates());
        $hasGrouping = !empty($intent->groupBy());

        if ($hasAggregates && $hasGrouping) {

            $groups = [];

            foreach ($rows as $row) {

                $groupValues = [];

                foreach ($intent->groupBy() as $groupField) {
                    $groupValues[$groupField] = $row[$groupField] ?? null;
                }

                $metrics = [];

                foreach ($intent->aggregates() as $aggregate) {
                    $alias = $aggregate->alias()
                        ?? "aggregate_" . strtolower($aggregate->function());

                    $metrics[strtolower($aggregate->function())] =
                        $row[$alias] ?? null;
                }

                $groups[] = [
                    "group" => $groupValues,
                    "metrics" => $metrics
                ];
            }

            return [
                "type" => count($intent->aggregates()) > 1
                    ? "grouped_multi_aggregate"
                    : "grouped_aggregate",
                "groups" => $groups
            ];
        }

        if ($hasAggregates && count($intent->aggregates()) > 1 && !$hasGrouping) {
            $metrics = [];

            foreach ($intent->aggregates() as $aggregate) {
                $alias = $aggregate->alias()
                    ?? "aggregate_" . strtolower($aggregate->function());

                $metrics[strtolower($aggregate->function())] =
                    $rows[0][$alias] ?? null;
            }

            return [
                "type" => "multi_aggregate",
                "metrics" => $metrics
            ];
        }

        if ($hasAggregates && !$hasGrouping) {
            $aggregate = $intent->aggregates()[0];
            $alias = $aggregate->alias()
                ?? "aggregate_" . strtolower($aggregate->function());

            return [
                "type" => "aggregate",
                "function" => strtolower($aggregate->function()),
                "value" => $rows[0][$alias] ?? null
            ];
        }

        return [
            "type" => "list",
            "rows" => $rows
        ];
    }
}