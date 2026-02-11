<?php

use Alternate\Insight\Schema\TableDefinition;
use Alternate\Insight\Schema\ColumnDefinition;

return new TableDefinition(
    name: 'beleg',
    description: 'Core document table for orders, invoices, credit notes, offers and other commercial documents.',
    primaryKey: 'bel_lfdnr',
    columns: [
        new ColumnDefinition(
            name: 'bel_lfdnr',
            description: 'Internal unique document identifier.',
            type: 'integer'
        ),

        new ColumnDefinition(
            name: 'bel_nr',
            description: 'External document number.',
            type: 'integer'
        ),

        new ColumnDefinition(
            name: 'bel_typ',
            description: 'Document type.',
            type: 'enum',
            values: [
                1 => 'Order',
                2 => 'Pakbon',
                4 => 'Factuur',
                5 => 'Creditnota',
                6 => 'Bestelbon',
                7 => 'Inboek',
                9 => 'Offerte',
            ]
        ),

        new ColumnDefinition(
            name: 'bel_datum',
            description: 'Document date (YYYYMMDD).',
            type: 'date'
        ),

        new ColumnDefinition(
            name: 'bel_kontonr',
            description: 'Customer account number.',
            type: 'integer'
        ),

        new ColumnDefinition(
            name: 'bel_istwert',
            description: 'Total document value.',
            type: 'decimal'
        ),

        new ColumnDefinition(
            name: 'bel_restwert',
            description: 'Outstanding amount.',
            type: 'decimal'
        ),
    ]
);
