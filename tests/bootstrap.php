<?php

/**
 * This file is part of tomkyle/kurzelinks
 *
 * Link shortener using the kurzelinks.de API. Supports PSR-6 caches and rate limits.
 */

$root_path = dirname(__DIR__);
$autoloader = $root_path . '/vendor/autoload.php';
if (!is_readable($autoloader)) {
    die(sprintf("\nMissing Composer's Autoloader '%s'; Install Composer dependencies first.\n\n", $autoloader));
}
