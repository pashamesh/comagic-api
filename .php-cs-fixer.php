<?php

use PhpCsFixer\Finder;
use PhpCsFixer\Config;

$finder = Finder::create()
    ->in([
        'src',
        'tests'
    ]);

$config = new Config();

return $config
    ->setRules([
        '@PSR12' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_trailing_comma_in_singleline' => true,
        'no_unused_imports' => true,
        'trailing_comma_in_multiline' => [
            'elements' => ['arrays'],
        ],
        'blank_line_before_statement' => ['statements' => ['return']],
        'braces' => [
            'position_after_functions_and_oop_constructs' => 'next',
        ],
        'php_unit_construct' => true,
        'php_unit_method_casing' => true,
        'php_unit_mock_short_will_return' => true,
        'php_unit_test_annotation' => true,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'this'],
    ])
    ->setFinder($finder);
