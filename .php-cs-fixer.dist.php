<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'method_chaining_indentation' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_class_elements' => ['order' => ['use_trait', 'case', 'constant_public', 'constant_protected', 'constant_private', 'property_public', 'property_protected', 'property_private', 'construct', 'destruct', 'magic', 'phpunit', 'method_public', 'method_protected', 'method_private']],
        'return_assignment' => true,
        'single_line_empty_body' => true,
        'concat_space' => ['spacing' => 'one'],
    ])
    ->setFinder($finder)
;
