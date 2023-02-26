<?php declare(strict_types=1);

return [
    'plugin' => [
        'name' => 'BlogHub by rat.md',
        'description' => 'Extends RainLab.Blog with Comments, Tags, Custom Meta Fields, Archives, Statistics, Views and more.',
    ],

    'components' => [
        'bloghub_group' => 'BlogHub Settings',

        'base' => [
            'label' => 'Base Configuration',
            'comment' => 'The Base BlogHub configuration, should be included on the base CMS layouts.',
            'archive_author' => 'Author CMS Page',
            'archive_author_comment' => 'Name of the CMS Page used for the author archive.',
            'archive_date' => 'Date CMS Page',
            'archive_date_comment' => 'Name of the CMS Page used for the date archive.',
            'archive_tag' => 'Tag CMS Page',
            'archive_tag_comment' => 'Name of the CMS Page used for the tag archive.',
            'author_slug' => 'Use Author Slug only',
            'author_slug_comment' => 'Uses the author_slug backend column field only.',
            'date_invalid' => '404 on invalid dates',
            'date_invalid_comment' => 'Shows the 404 error message on invalid dates.',
            'date_empty' => '404 on empty dates',
            'date_empty_comment' => 'Shows the 404 error message on empty date archives.',
            'tag_multiple' => 'Allow multiple tags',
            'tag_multiple_comment' => 'Allows multiple tag queries using + or , on the tag archive pages.',
        ],
        'author' => [
            'label' => 'Posts by Author',
            'comment' => 'Displays a list of posts by author.',
            'filter' => 'Author filter',
            'filter_comment' => 'Enter a author login name or URL parameter to filter the posts by.',
        ],
        'comment_count' => [
            'label' => 'Posts by Comments',
            'comment' => 'Displays a list of posts by the comments counter.'
        ],
        'comments_list' => [
            'label' => 'Comments List',
            'comment' => 'Displays a list of comments on the page.',
            'exclude_posts' => 'Exclude Posts',
            'exclude_posts_description' => 'Exclude specific post ids or post slugs (comma-separated list).',
            'amount' => 'Comment Amount',
            'amount_description' => 'The amount of comments to be passed to the page.',
            'amount_validation' => 'Invalid format of the the amount value.',
            'only_favorites' => 'Show Favorites only',
            'only_favorites_description' => 'Shows only comments which has been marked as favorites by the authors.',
            'default_tab' => 'Default Tab',
            'default_tab_comment' => 'The default tab shown in the widget.'
        ],
        'comments_section' => [
            'label' => 'Comments Section',
            'comment' => 'Display a comments section with comment form on the single post CMS page.',
            'group_form' => 'Comment Form',
            'post_slug' => 'Post filter',
            'post_slug_comment' => 'Enter a post slug or URL parameter to filter the comments by.',
            'comments_per_page' => 'Comments per Page',
            'comments_order' => 'Comment order',
            'comments_order_comment' => 'Attribute on which the comments should be ordered.',
            'comments_hierarchy' => 'Display Hierarchically',
            'comments_hierarchy_comment' => 'Shows replies hierarchically below the parent comment or flat using quotation.',
            'comments_anchor' => 'Container Anchor',
            'comments_anchor_comment' => 'The ID of the main comment container, used as URL anchor on the pagination links.',
            'pin_favorites' => 'Pin Favorites',
            'pin_favorites_comment' => 'Pin Author-Favorites comments on top of the comment list.',
            'hide_on_dislike' => 'Hide disliked comments',
            'hide_on_dislike_comment' => 'Hide disliked comments, use either an absolute number or start your number with a colon to define a relation to the likes.',
            'form_position' => 'Form Position',
            'form_position_comment' => 'Change the Position for the Comment Form section.',
            'form_position_above' => 'Above the comments',
            'form_position_below' => 'Below the comments',
            'disable_form' => 'Disable Comment Form',
            'disable_form_comment' => 'Disables the comment submit form, regardless of the post option.',
        ],
        'date' => [
            'label' => 'Posts by Date',
            'comment' => 'Displays a list of posts by date.',
            'filter' => 'Date filter',
            'filter_comment' => 'Enter a specific date or URL parameter to filter the posts by.',
        ],
        'post' => [
            'date_range' => 'Default date range',
            'date_range_comment' => 'Change the default date range used for the graphs.',
            '7days' => 'Last 7 days',
            '14days' => 'Last 14 days',
            '31days' => 'Last 31 days',
            '3months' => 'Last 3 months',
            '6months' => 'Last 6 months',
            '12months' => 'Last 12 months',
            'views_visitors' => 'Views / Visitors',
            'views' => 'Views',
            'visitors' => ' Visitors',
            'published_posts' => 'Published Posts',
            'default_order' => 'Default order',
            'default_order_comment' => 'Change the default order used for the post list',
            'by_published' => 'By Published Date',
            'by_views' => 'By Views',
            'by_visitors' => 'By Visitors',
            'total' => 'Total Posts',
            'published' => 'Published',
            'scheduled' => 'Scheduled',
            'draft' => 'Draft',
            'posts_list' => 'Posts List',
        ],
        'tag' => [
            'label' => 'Posts by Tags',
            'comment' => 'Displays a list of posts by tag.',
            'filter' => 'Tag filter',
            'filter_comment' => 'Enter a tag slug or URL parameter to filter the posts by.',
        ],
        'tags' => [
            'label' => 'Tags List',
            'comment' => 'Displays a list of (promoted) blog tags.',
            'tags_page' => 'Tag Archive Page',
            'tags_page_comment' => 'Name of the CMS Page used for the tag archive.',
            'only_promoted' => 'Promoted only',
            'only_promoted_comment' => 'Display only promoted tags',
            'amount' => 'Tag Amount',
            'amount_description' => 'The amount of tags to be passed to the list.',
            'amount_validation' => 'Invalid format of the the amount value.',
            'view' => 'Tag View',
            'view_comment' => 'Change the View of the Tag list'
        ],
        'deprecated' => [
            'authors_label' => '[OLD] Posts by Author',
            'authors_comment' => '[DEPRECATED] - Please use "Posts By Author" above.',
            'dates_label' => '[OLD] Posts by Date',
            'dates_comment' => '[DEPRECATED] - Please use "Posts By Date" above.',
            'tags_label' => '[OLD] Posts by Tag',
            'tags_comment' => '[DEPRECATED] - Please use "Posts by Tags" above.',
        ],
    ],

    'frontend' => [
        'comments' => [
            'username' => 'Your Username',
            'email' => 'Your Mail address',
            'title' => 'Your Comment Title',
            'comment' => 'Your Comment',
            'comment_markdown_hint' => 'You can use the Markdown syntax to style your comment.',
            'captcha' => 'Captcha Code',
            'captcha_reload' => 'Reload Captcha', 
            'captcha_placeholder' => 'Enter the code from the image', 
            'submit_comment' => 'Write a new Comment',
            'cancel_reply' => 'Cancel this Reply',
            'submit_reply' => 'Reply to this comment',
            'approve' => 'Approve',
            'approve_title' => 'Approve this comment',
            'reject' => 'Reject',
            'reject_title' => 'Reject this comment',
            'spam' => 'Mark as Spam',
            'spam_title' => 'Mark this comment as spam',
            'like' => 'Like',
            'like_title' => 'Like this Comment',
            'dislike' => 'Dislike',
            'dislike_title' => 'Dislike this Comment',
            'favorite' => 'Favorite',
            'favorite_title' => 'Favorite this Comment',
            'unfavorite' => 'Unfavorite',
            'unfavorite_title' => 'Unfavorite this Comment',
            'reply' => 'Reply',
            'reply_title' => 'Reply to this Comment',
            'disabled_open' => 'You\'re not allowed to comment on this post.',
            'disabled_restricted' => 'You must be logged-in to comment on this post.',
            'disabled_private' => 'Only registered backend users are allowed to comment on this post.',
            'disabled_closed' => 'The comment section for this post has been closed.',
            'awaiting_moderation' => 'Awaiting Moderation',
            'previous' => 'Previous',
            'next' => 'Next',
            'replyto' => 'Reply to :name',
            'comment_by' => 'Comment by',
            'reply_by' => 'Reply by',
            'by' => 'By',
            'on' => 'on',
        ],
        'errors' => [
            'unknown_post' => 'The passed post id or slug is unknown or invalid.',
            'missing_form_id' => 'The component id is missing or invalid.',
            'form_disabled' => 'The comment form is disabled on this post.',
            'not_allowed_to_comment' => 'You\'re not allowed to comment or reply on this post.',
            'invalid_csrf_token' => 'The passed CSRF token is invalid. Please reload the page and try again.',
            'invalid_validation_code' => 'The passed Comment Validation code is invalid. Please reload the page and try again.',
            'invalid_captcha' => 'The passed Captcha Code was wrong.',
            'honeypot_filled' => 'The passed data seems strange, please try again later.',
            'tos_not_accepted' => 'You need to accept the Terms of Service to comment on this post.',
            'parent_not_found' => 'The parent comment on which you tried to reply does not exist or has been deleted.',
            'parent_invalid' => 'The parent comment on which you tried to reply is invalid or has been moved.',
            'not_allowed_to' => 'You are not allowed to call this action.',
            'moderate_permission' => 'You are not allowed to moderate comments.',
            'invalid_sttus' => 'The passed comment status is invalid.',
            'unknown_comment' => 'The passed comment does not exist (anymore).',
            'disabled_method' => 'This function has been disabled by the website administrator.',
            'no_permissions_for' => 'You don\'t have the permission to call this action.',
            'missing_comment_id' => 'The comment id is missing or invalid.',
            'invalid_comment_id' => 'The passed comment id does not exist.',
            'unknown_error' => 'An unknown error occured, please try again later.'
        ],
        'success' => [
            'update_status' => 'The comment status could be successfully updated.'
        ]
    ],

    'model' => [
        'comments' => [
            'label' => 'Comments',
            'manage' => 'Manage Comments',
            'recordName' => 'Comment',
            'status' => 'Comment Status',
            'statusColumn' => 'Status',
            'statusComment' => 'Change the current Comment Status',
            'statusPending' => 'Pending',
            'statusApproved' => 'Approved',
            'statusRejected' => 'Rejected',
            'statusSpam' => 'Spam',
            'title' => 'Comment Title',
            'titleComment' => 'The title of the comment (depending on the Bloghub configuration).',
            'content' => 'Comment Content',
            'contentComment' => 'The plain content of the comment.',
            'favorite' => 'Favorite Comment',
            'favoriteComment' => 'Favorite comments are highlighted and may be shown on top of the comments list.',
            'favoriteColumn' => 'Favorite',
            'likes' => 'Likes',
            'dislikes' => 'Disikes',
            'author' => 'Auther Username',
            'authorComment' => 'The username of the author, when not written by a logged-in user.',
            'authorEmail' => 'Author E-Mail address',
            'authorEmailComment' => 'The E-Mail address of the author, when not written by a logged-in user.',
            'post_visibility' => [
                'label' => 'Comments Visibility',
                'comment' => 'Show  or Hide the comments section on this post.'
            ],
            'post_mode' => [
                'label' => 'Comment Mode',
                'comment' => 'Change the comment mode for this post.',
                'open' => 'Open (Everyone can comment)',
                'restricted' => 'Restricted (Only logged-In Users can comment)',
                'private' => 'Private (Only logged-in backend users can comment)',
                'closed' => 'Closed (Noone can comment)'
            ],
            'guest' => 'Guest',
            'seconds_ago' => 'A few seconds ago',
            'x_ago' => ':amount :format ago',
            'no_comment' => 'No comment available', 
            'no_further_comments' => 'No further comments available', 
        ],
        'post' => [
            'read_time' => 'Read Time: :min minutes :sec seconds',
            'read_time_sec' => 'Read Time: :sec seconds',
            'published_seconds_ago' => 'Published a few seconds ago.',
            'published_ago' => 'Published :amount :format ago.',
            'published_format_years' => 'years',
            'published_format_months' => 'months',
            'published_format_days' => 'days',
            'published_format_hours' => 'hours',
            'published_format_minutes' => 'minutes',
            'statistics' => 'Post Statistics',
        ],
        'tags' => [
            'label' => 'Tags',
            'manage' => 'Manage Tags',
            'recordName' => 'Tag',
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

    'permissions' => [
        'access_comments' => 'Manage the blog comments',
        'access_comments_comment' => 'Allows access to the comments submenu for all posts.',
        'manage_post_settings' => 'Manage post-related comment settings',
        'moderate_comments' => 'Moderate blog comments',
        'delete_commpents' => 'Allowed to delete published comments',
        'access_tags' => 'Manage the blog tags',
        'access_tags_comment' => 'Allows to access the tags submenu and to set the post tags itself for all posts.',
        'promote_tags' => 'Allows to promote tags',
    ],

    'sorting' => [
        'bloghub_views_asc' => 'Views (ascending)',
        'bloghub_views_desc' => 'Views (descending)',
        'bloghub_unique_views_asc' => 'Unique Views (ascending)',
        'bloghub_unique_views_desc' => 'Unique Views (descending)',
        'bloghub_comments_count_asc' => 'Number of Comments (ascending)',
        'bloghub_comments_count_desc' => 'Number of Comments (descending)',
        'created_at_desc' => 'Published (descending)',
        'created_at_asc' => 'Published (ascending)',
        'comments_count_desc' => 'Comments Counter (descending)',
        'comments_count_asc' => 'Comments Counter (ascending)',
        'likes_desc' => 'Likes Counter (descending)',
        'likes_asc' => 'Likes Counter (ascending)',
        'dislikes_desc' => 'Dislikes Counter (descending)',
        'dislikes_asc' => 'Dislikes Counter (ascending)',
    ],

    'settings' => [
        'config' => [
            'label' => 'BlogHub',
            'description' => 'Manage the BlogHub related settings.'
        ],

        'comments' => [
            'tab' => 'Comments',
            'general_section' => 'General Settings',
            'comment_form_section' => 'Form Settings',

            'author_favorites' => [
                'label' => 'Author Favorites',
                'comment' => 'Allow authors to favourite user comments.'
            ],
            'like_comment' => [
                'label' => 'Like Comments',
                'comment' => 'Enable the Like button on each user comment.'
            ],
            'dislike_comment' => [
                'label' => 'Dislike Comments',
                'comment' => 'Enable the Dislike button on each user comment.'
            ],
            'restrict_to_users' => [
                'label' => 'Like & Dislike only for users',
                'comment' => 'Restrict the Like and Dislike functions to logged in users only.'
            ],
            'guest_comments' => [
                'label' => 'Guest Comments',
                'comment' => 'Allow guests to comment on all enabled posts.'
            ],
            'moderate_guest_comments' => [
                'label' => 'Moderate Guest Comments',
                'comment' => 'Moderate each guest comment, before it is published and visible to everyone.'
            ],
            'moderate_user_comments' => [
                'label' => 'Moderate User Comments',
                'comment' => 'Moderate each frontend user comment, before it is published and visible to everyone.'
            ],
            'form_comment_title' => [
                'label' => 'Comment Title Field',
                'comment' => 'Enable and Use the comment title field.',
            ],
            'form_comment_markdown' => [
                'label' => 'Comment Markdown',
                'comment' => 'Allow to use markdown in the comment body field.',
            ],
            'form_comment_honeypot' => [
                'label' => 'Comment Honeypot',
                'comment' => 'Add a Honeypot field to protect your comments from the simplest bots.',
            ],
            'form_comment_captcha' => [
                'label' => 'Comment Captcha',
                'comment' => 'Add a GREGWAR Captcha field to protect your comments from some bots.',
            ],
            'form_tos_checkbox' => [
                'label' => 'Require Terms of Service',
                'comment' => 'Shows a Terms of Service agreement checkbox below the comment form.',
            ],
            'form_tos_hide_on_user' => [
                'label' => 'Hide for known Users',
                'comment' => 'Hides the Terms of Service checkbox for loggedin users and backend users.',
            ],
            'form_tos_type' => [
                'label' => 'Terms of Service Type',
                'cms_page' => 'CMS Page',
                'static_page' => 'Static Page'
            ],
            'form_tos_label' => [
                'label' => 'Terms of Service Label',
                'default' => 'I\'ve read and agree to the [Terms of Service]',
                'comment' => 'The text within the square brackets will link to the specified page.'
            ],
            'form_tos_page' => [
                'cmsLabel' => 'Terms of Service - CMS Page',
                'staticLabel' => 'Terms of Service - Static Page',
                'emptyOption' => '-- Select a Terms of Service Page --',
                'comment' => 'Select the desired page to link to, or leave empty to show the plain label only.'
            ]
        ],

        'meta' => [
            'defaultTab' => 'Meta Data',
            'label' => 'Custom Meta Fields',
            'description' => 'Manage the global custom meta fields for your posts.',
            'prompt' => 'Add a new Meta Field',

            'hint' => [
                'label' => 'Make sure your Field Names are unique',
                'comment' => 'The custom meta fields configured here will be overriden by the meta fields configured in the theme.yaml template file. Thus, keep your keys unique!'
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
    ],

    'widgets' => [
        'comments_list' => [
            'label' => 'BlogHub - Comments List'
        ],
        'posts_list' => [
            'label' => 'BlogHub - Posts List'
        ],
        'posts_statistics' => [
            'label' => 'BlogHub - Posts Statistics'
        ]
    ]
];
