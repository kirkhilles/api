<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('vendor')  // Automatically ignore vendor!
    ->exclude('storage'); // Ignore other temp folders if you have them

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true, // Enforce the modern PHP standard
        'array_syntax' => ['syntax' => 'short'], // Force [] instead of array()
    ])
    ->setFinder($finder);