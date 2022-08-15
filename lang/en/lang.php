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
