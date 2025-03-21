<?php

$finder = (new PhpCsFixer\Finder())
    ->in(['src', 'tests'])
    ->exclude(['var']);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony'                   => true,
        '@PSR12'                     => true,
        'array_indentation'                      => true,
        'combine_consecutive_unsets'             => true,
        'phpdoc_order'                           => true,
        'no_superfluous_phpdoc_tags'             => false,
        'multiline_whitespace_before_semicolons' => true,
        'single_quote'                           => true,
        'align_multiline_comment'    => true,
        'ordered_imports'            => true,
        'ordered_class_elements'     => true,
        'binary_operator_spaces'     => [
            'operators' => [
                '=>' => 'align_single_space_minimal',
                '='  => 'align_single_space_minimal',
            ],
        ],
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'class_attributes_separation' => [
            'elements' => [
                'method'   => 'one',
                'property' => 'none'
            ],
        ],
        'braces' => [
            'allow_single_line_closure' => true,
        ],
        'concat_space' => [
            'spacing' => 'one'
        ],
        'declare_equal_normalize'   => true,
        'function_typehint_space'   => true,
        'single_line_comment_style' => [
            'comment_types' => ['hash']
        ],
        'include'              => true,
        'lowercase_cast'       => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'curly_brace_block',
                'extra',
                'throw',
                'use',
            ]
        ],
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_spaces_around_offset'                     => true,
        'no_whitespace_before_comma_in_array'         => true,
        'no_whitespace_in_blank_line'                 => true,
        'object_operator_without_whitespace'          => true,
        'ternary_operator_spaces'                     => true,
        'trim_array_spaces'                           => true,
        'unary_operator_spaces'                       => true,
        'whitespace_after_comma_in_array'             => true,
        'space_after_semicolon'                       => true,
        'no_unused_imports'                           => true,
    ])
    ->setFinder($finder);
