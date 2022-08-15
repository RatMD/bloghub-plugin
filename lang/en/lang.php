<?php declare(strict_types=1);

return [
    'plugin' => [
        'name' => 'BlogHub - Template Extension',
        'description' => 'Required extension to run and use the BlogHub template.',
    ],

    'backend' => [
        'tags' => [
            'label' => 'Tags',
            'singular' => 'Tag',
            'plural' => 'Tag',
            'slug' => 'Slug',
            'title' => 'Title',
            'description' => 'Description',
            'descriptionComment' => 'Supported themes will show the description on the Tag archive pages.',
            'promote' => 'Promoted Tag',
            'promoteComment' => 'Supported themes will show promoted tags in a different way.',
            'color' => 'Color',
            'colorComment' => 'Supported themes will use the color to highlight this tag.',
            'posts' => 'Assigned Posts',
            'postsEmpty' => 'No posts available.'
        ],

        'meta' => [
            'tab' => 'Meta Data'
        ]
    ]
];
