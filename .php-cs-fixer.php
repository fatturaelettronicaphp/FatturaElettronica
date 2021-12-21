<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->notPath('build/*')
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2'                             => true,
        'array_indentation'                 => true,
        'array_syntax'                      => ['syntax' => 'short'],
        'no_extra_consecutive_blank_lines'  => true,
        'ordered_imports'                   => ['sortAlgorithm' => 'alpha'],
        'no_unused_imports'                 => true,
        'not_operator_with_successor_space' => true,
        'trailing_comma_in_multiline_array' => true,
        'phpdoc_scalar'                     => true,
        'unary_operator_spaces'             => true,
        'binary_operator_spaces'            => [
            'default' => 'align',
        ],
        'blank_line_before_statement'       => [
            'statements' => [
                'break',
                'continue',
                'declare',
                'return',
                'throw',
                'try',
            ],
        ],
        'phpdoc_single_line_var_spacing'    => true,
        'phpdoc_var_without_name'           => true,
        'method_argument_space'             => [
            'on_multiline'                     => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => true,
        ],
        'indentation_type'                  => true,
        'method_chaining_indentation'       => true,
    ])
    ->setFinder($finder);
