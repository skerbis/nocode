<?php

namespace KLXM\nocode;

// Prevent direct access
if (!defined('REDAXO')) {
    die('Direct access denied!');
}

// Register default templates when packages are included
rex_extension::register('PACKAGES_INCLUDED', function() {
    try {
        // Register news detail template
        Template::registerTemplate(
            'news_detail',
            'uikit3',
            [
                'title' => ['type' => 'text', 'required' => true],
                'text' => ['type' => 'textarea', 'required' => true],
                'image' => ['type' => 'media', 'required' => false],
                'date' => ['type' => 'date', 'required' => false],
                'author' => ['type' => 'text', 'required' => false],
                'category' => ['type' => 'text', 'required' => false],
                'gallery' => ['type' => 'medialist', 'required' => false],
                'documents' => ['type' => 'medialist', 'required' => false]
            ],
            'news/detail.php'
        );

        // Register news list template
        Template::registerTemplate(
            'news_list',
            'uikit3',
            [
                'items' => [
                    'type' => 'array',
                    'required' => true,
                    'fields' => [
                        'title' => ['type' => 'text', 'required' => true],
                        'text' => ['type' => 'textarea', 'required' => true],
                        'image' => ['type' => 'media', 'required' => false],
                        'date' => ['type' => 'date', 'required' => false],
                        'url' => ['type' => 'url', 'required' => false]
                    ]
                ],
                'options' => [
                    'type' => 'array',
                    'required' => false,
                    'fields' => [
                        'columns' => [
                            'type' => 'select', 
                            'options' => [1, 2, 3, 4], 
                            'default' => 3
                        ],
                        'layout' => [
                            'type' => 'select', 
                            'options' => ['card', 'list', 'masonry'], 
                            'default' => 'card'
                        ],
                        'showDate' => [
                            'type' => 'boolean', 
                            'default' => true
                        ]
                    ]
                ]
            ],
            'news/list.php'
        );

    } catch (\Exception $e) {
        // Log error but don't throw exception to prevent REDAXO from breaking
        rex_logger::logException($e);
    }
}, rex_extension::LATE);
