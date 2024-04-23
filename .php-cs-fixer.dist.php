<?php

$finder = (new PhpCsFixer\Finder())
    ->in(['src', 'tests'])
    ->exclude(['var']);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony'                   => true,
        '@PSR12'                     => true,
        'align_multiline_comment'    => true,
        'ordered_imports'            => true,
        'ordered_class_elements'     => true,
        'phpdoc_order'               => true,
        'no_superfluous_phpdoc_tags' => false,
        'binary_operator_spaces'     => [
            'operators' => [
                '=>' => 'align_single_space_minimal',
                '='  => 'align_single_space_minimal',
            ],
        ],
        'array_syntax' => [
            'syntax' => 'short',
        ],
    ])
    ->setFinder($finder);
