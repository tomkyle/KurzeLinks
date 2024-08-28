<?php

/**
 * tomkyle/kurzelinks
 *
 * Create short links with kurzelinks.de
 */

$root_path = dirname(__DIR__);
$autoloader = $root_path . '/vendor/autoload.php';
if (!is_readable($autoloader)) {
    die(sprintf("\nMissing Composer's Autoloader '%s'; Install Composer dependencies first.\n\n", $autoloader));
}
