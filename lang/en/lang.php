<?php declare(strict_types=1);

return [
    'plugin' => [
        'name' => 'RatMD BlogHub',
        'description' => 'Extends RainLab\'s Blog extension with custom meta details, additional archives and more.',
    ],

    'model' => [
        'tags' => [
            'label' => 'Tags',
            'slug' => 'Slug',
            'slugComment' => 'Tag slugs are used for the archive pages on the frontend.',
            'title' => 'Title',
            'titleComment' => 'Supporting themes may show the tag title instead of the tag slug.',
            'description' => 'Description',
            'descriptionComment' => 'Supporting themes may show the description text on the tag archive pages.',
            'promote' => 'Promoted Tag',
            'promoteComment' => 'Supporting themes may highlight promoted tags in a special way.',
            'color' => 'Color',
            'colorComment' => 'Supporting themes may use this color to highlight this tag.',
            'posts' => 'Assigned Posts',
            'postsComment' => 'The single posts assigned to this tag.',
            'postsEmpty' => 'No posts available.',
            'postsNumber' => 'No of Posts'
        ],
        'users' => [
            'displayName' => 'Display Name',
            'displayNameComment' => 'A custom version of your name, supporting themes may show them on your posts.',
            'authorSlug' => 'Author Slug',
            'authorSlugComment' => 'Author slugs are used for the archive on the frontend (instead of the login name).',
            'aboutMe' => 'About Me',
            'aboutMeDescription' => 'A small description about yourself, supporting themes may show them on your posts.'
        ],
        'visitors' => [
            'views' => 'Views / Unique'
        ]
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

    'settings' => [
        'defaultTab' => 'Meta Data',
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
];
