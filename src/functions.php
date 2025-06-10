<?php

/**
 * This file is part of tomkyle/kurzelinks
 *
 * Link shortener using the kurzelinks.de API. Supports PSR-6 caches and rate limits.
 */

namespace App;

if (!function_exists('\App\dotenv')) {
    /**
     * Map getenv-like calls to $_SERVER.
     *
     * @param  string $var     Enviroment variable name
     * @return mixed Enviroment variable value
     */
    function dotenv(string $var, mixed $default = null)
    {
        return $_SERVER[ $var ] ?? null;
    }
}
