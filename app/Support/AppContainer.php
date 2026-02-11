<?php

namespace Alternate\Insight\Support;

use Arc\Base\Base;
use Arc\Base\Config\MySqlConfigBuilder;
use Arc\Guard\Guard;
use Arc\Vault\Vault;

use Throwable;

final class AppContainer {
    private static bool $booted = false;

    public static function boot(): void {
        if(self::$booted) return;

        try {
            // Load environment variables
            Vault::create(__DIR__ . "/../../.env");

            // Start a database connection
            Base::create(
                MysqlConfigBuilder::make()
                    ->host(Vault::instance()->require("DB4_HOST"))
                    ->database(Vault::instance()->require("DB4_DATABASE"))
                    ->username(Vault::instance()->require("DB4_USERNAME"))
                    ->password(Vault::instance()->require("DB4_PASSWORD"))
                    ->build()
            );

            // Start Guard and load default rules
            Guard::create();

        } catch (Throwable $e) {
            die("Application failed to start: " . $e->getMessage());
        }

        self::$booted = true;
    }
}