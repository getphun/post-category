<?php
/**
 * post-category config file
 * @package post-category
 * @version 0.0.1
 * @upgrade true
 */

return [
    '__name' => 'post-category',
    '__version' => '0.0.1',
    '__git' => 'https://github.com/getphun/post-category',
    '__files' => [
        'modules/post-category/config.php'   => [ 'install', 'remove', 'update' ],
        'modules/post-category/_db'          => [ 'install', 'remove', 'update' ],
        'modules/post-category/model'        => [ 'install', 'remove', 'update' ],
        'modules/post-category/library'      => [ 'install', 'remove', 'update' ],
        'modules/post-category/meta'         => [ 'install', 'remove', 'update' ],
        'modules/post-category/event'        => [ 'install', 'remove' ],
        'modules/post-category/controller/RobotController.php' => [ 'install', 'remove', 'update' ],
        'modules/post-category/controller/CategoryController.php' => [ 'install', 'remove' ],
        'theme/site/post/category'           => [ 'install', 'remove' ]
    ],
    '__dependencies' => [
        'site-param',
        'formatter',
        'site',
        'site-meta',
        '/db-mysql',
        '/robot'
    ],
    '_services' => [],
    '_autoload' => [
        'classes' => [
            'PostCategory\\Model\\PostCategory'       => 'modules/post-category/model/PostCategory.php',
            'PostCategory\\Model\\PostCategoryChain'  => 'modules/post-category/model/PostCategoryChain.php',
            'PostCategory\\Library\\Robot'       => 'modules/post-category/library/Robot.php',
            'PostCategory\\Meta\\Category'            => 'modules/post-category/meta/Category.php',
            'PostCategory\\Controller\\RobotController'  => 'modules/post-category/controller/RobotController.php',
            'PostCategory\\Controller\\CategoryController'    => 'modules/post-category/controller/CategoryController.php',
            'PostCategory\\Event\\CategoryEvent'      => 'modules/post-category/event/CategoryEvent.php'
        ],
        'files' => []
    ],
    '_routes' => [
        'site' => [
            'sitePostCategoryFeed' => [
                'rule' => '/post/category/feed.xml',
                'handler' => 'PostCategory\\Controller\\Robot::feed'
            ],
            'sitePostCategory' => [
                'rule' => '/post/category',
                'handler' => 'PostCategory\\Controller\\Category::index'
            ],
            
            'sitePostCategorySingleFeed' => [
                'rule' => '/post/category/:slug/feed.xml',
                'handler' => 'PostCategory\\Controller\\Robot::feedSingle'
            ],
            'sitePostCategorySingle' => [
                'priority' => 0,
                'rule' => '/post/category/:slug',
                'handler' => 'PostCategory\\Controller\\Category::single'
            ]
        ]
    ],
    'events' => [
        'post-category:created' => [
            'post-category' => 'PostCategory\\Event\\CategoryEvent::created'
        ],
        'post-category:updated' => [
            'post-category' => 'PostCategory\\Event\\CategoryEvent::updated'
        ],
        'post-category:deleted' => [
            'post-category' => 'PostCategory\\Event\\CategoryEvent::deleted'
        ]
    ],
    'formatter' => [
        'post-category' => [
            'name' => 'text',
            'about' => 'text',
            'updated' => 'date',
            'created' => 'date',
            'user' => [
                'type' => 'object',
                'model' => 'User\\Model\\User'
            ],
            'page' => [
                'type' => 'router',
                'params' => [
                    'for' => 'sitePostCategorySingle'
                ]
            ],
            'meta_title' => 'text',
            'meta_description' => 'text'
        ],
        'post' => [
            'category' => [
                'type' => 'chain',
                'model' => 'PostCategory\\Model\\PostCategory',
                'chain' => [
                    'model' => 'PostCategory\\Model\\PostCategoryChain',
                    'object' => 'post',
                    'parent' => 'post_category'
                ],
                'format' => 'post-category'
            ]
        ]
    ],
    'robot' => [
        'sitemap' => [
            'post-category' => 'PostCategory\\Library\\Robot::sitemap'
        ],
        'feed' => [
            'post-category' => 'PostCategory\\Library\\Robot::feed'
        ]
    ]
];