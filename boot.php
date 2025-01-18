<?php

namespace KLXM\nocode;

// Prevent direct access
if (!defined('REDAXO')) {
    die('Direct access denied!');
}

// Register default templates
rex_addon::get('nocode')->setProperty('templates_registered', false);

rex_extension::register('PACKAGES_INCLUDED', function() {
    // Check if templates are already registered to prevent double registration
    if (rex_addon::get('nocode')->getProperty('templates_registered')) {
        return;
    }

    try {
        // Register detail template
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

        // Register list template
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

        // Mark templates as registered
        rex_addon::get('nocode')->setProperty('templates_registered', true);

    } catch (\Exception $e) {
        // Log error but don't throw exception to prevent REDAXO from breaking
        rex_logger::logException($e);
    }
}, rex_extension::LATE);

// Create necessary directories on addon installation
if (rex::isBackend()) {
    rex_extension::register('ADDON_INSTALLED', function($ep) {
        if ($ep->getParam('addon')->getName() === 'nocode') {
            $templateDir = rex_path::addonData('nocode', 'templates/uikit3/news');
            if (!file_exists($templateDir)) {
                rex_dir::create($templateDir);
            }
        }
    });
}
