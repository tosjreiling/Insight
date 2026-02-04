<?php

namespace Alternate\Insight\Support;

final class AppContainer {
    private static bool $booted = false;

    public static function boot(): void {
        if(self::$booted) return;

        echo "Insight booted successfully.\n";

        self::$booted = true;
    }
}