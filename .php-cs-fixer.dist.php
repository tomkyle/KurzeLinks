<?php
$header = <<<EOF
tomkyle/kurzelinks

Create short links with kurzelinks.de
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__,
        __DIR__ . '/bin',
        __DIR__ . '/src',
        __DIR__ . '/tests'
    ]);

return (new PhpCsFixer\Config())->setRules([
    '@PER-CS' => true,
    'header_comment' => [
        'comment_type' => 'PHPDoc',
        'header' => $header,
        'location' => 'after_open',
        'separate' => 'both',
    ]
])->setFinder($finder);
