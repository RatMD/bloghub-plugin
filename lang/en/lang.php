<?php declare(strict_types=1);

return [
    'plugin' => [
        'name' => 'RatMD BlogHub',
        'description' => 'Extends RainLab\'s Blog extension with custom meta details, additional archives and more.',
    ],

    'components' => [
        'authors_title' => 'Posts by Author',
        'authors_description' => 'Displays a list of posts by author.',
        'author_filter' => 'Author filter',
        'author_filter_description' => 'Enter a author login name or URL parameter to filter the posts by.',
        'dates_title' => 'Posts by Date',
        'dates_description' => 'Displays a list of posts by date.',
        'date_filter' => 'Date filter',
        'date_filter_description' => 'Enter a specific date or URL parameter to filter the posts by.',
        'tag_title' => 'Posts by Tag',
        'tag_description' => 'Displays a list of posts by tag.',
        'tag_filter' => 'Tag filter',
        'tag_filter_description' => 'Enter a tag slug or URL parameter to filter the posts by.',
        'tags_title' => 'Tags List',
        'tags_description' => 'Displays a list of blog tags on the page.',
        'tags_page' => 'Tag page',
        'tags_page_description' => 'Name of the tag page file for the tag links. This property is used by the default component partial.',
    ],

    'sorting' => [
        'bloghub_views_asc' => 'Views (ascending)',
        'bloghub_views_desc' => 'Views (descending)',
        'bloghub_unique_views_asc' => 'Unique Views (ascending)',
        'bloghub_unique_views_desc' => 'Unique Views (descending)'
    ],

    'backend_users' => [
        'display_name' => [
            'label' => 'Display Name',
            'description' => 'Change the name shown on the frontend.',
        ],
        'author_slug' => [
            'label' => 'Author Slug',
            'description' => 'Change the author slug used on the frontend archives (Leave empty to use "Login").',
        ]
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
        ],

        'settings' => [
            'label' => 'Custom Meta Data',
            'description' => 'Manage the global custom meta data for your posts.',
            'prompt' => 'Add a new Meta Field',

            'hint' => [
                'label' => 'Make sure your Custom Meta Names are unique',
                'comment' => 'The custom meta data configured here will be overriden by the meta data configured in the theme.yaml template file. Thus, keep your keys unique!'
            ],
            'name' => [
                'label' => 'Custom Meta Name',
                'comment' => 'The custom meta name as available on the frontend.'
            ],
            'type' => [
                'label' => 'Custom Meta Type',
                'comment' => 'The custom meta field type as available on the backend.'
            ],
            'config' => [
                'label' => 'Custom Meta Configuration',
                'comment' => 'Pass your field definition configuration here, as documented on the <a href="https://docs.octobercms.com/3.x/element/form/widget-taglist.html" target="_blank">OctoberCMS Docs</a>.'
            ],
            'types' => [
                'text' => 'Text Field',
                'number' => 'Number Field',
                'password' => 'Password Field',
                'email' => 'E-Mail Field',
                'textarea' => 'Textarea Field',
                'dropdown' => 'Dropdown Selector',
                'radio' => 'Radio Field',
                'balloon' => 'Balloon Selector',
                'checkbox' => 'Checkbox Field',
                'checkboxlist' => 'Checkbox List',
                'switch' => 'Switch Button',
                'codeeditor' => 'Code Editor',
                'colorpicker' => 'Color Picker',
                'datepicker' => 'Date/Time Picker',
                'fileupload' => 'File Upload Field',
                'markdown' => 'Markdown Editor',
                'mediafinder' => 'Media Finder',
                'richeditor' => 'Rich WYSIWYG Editor',
                'taglist' => 'Tag List',
            ]
        ]
    ]
];
