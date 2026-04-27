<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->exclude([
        'var',
        'vendor',
    ])
    ->name('*.php')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS' => true,
        '@PHP82Migration' => true,
        'array_syntax' => ['syntax' => 'short'],
        'single_quote' => true,
    ])
    ->setRiskyAllowed(false)
    ->setFinder($finder)
;
