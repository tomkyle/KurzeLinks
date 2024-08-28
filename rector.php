<?php

/**
 * tomkyle/kurzelinks
 *
 * Create short links with kurzelinks.de
 */

use Rector\Config\RectorConfig;

use Rector\Naming\Rector\Variable\RenameVariableToMatchMethodCallNameRector;
use Rector\Naming\ValueObject\RenameVariableToMatchMethodCallName;
use Rector\Naming\ValueObject\VariableRenaming;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/bin',
        __DIR__ . '/src'
    ])
    ->withRootFiles()
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        naming: true,
        codingStyle: true,
        codeQuality: true,
    );
