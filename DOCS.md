# BlogHub v1.3.0 - Documentation

Welcome on the BlogHub documentation page.

## Table of Contents
1. Requirements
2. Overview
4. Blog Comments
6. Blog Meta
5. Blog Tags
6. Template Components
9. Additional Menus
10. Additional Permissions
11. Extended Post Model
12. Extended User Model
13. Dashboard Widgets

## Requirements
- OctoberCMS v2/v3 (tested with latest version only)
- PHP 7.4+ / 8.0+
- RainLab.Blog
- **Supports:** RainLab.User (optional)
- **Supports:** RainLab.Pages (optional)
- **Supports:** RainLab.Translate (optional)

The optional plugins, as shown on the bottom of the list above, just extends existing or provides additional features and functions for the BlogHub plugin, but aren't required of course. The only required extension is **RainLab.Blog** itself.

## Overview
Our **BlogHub** extension adds a lot of functionallity to RainLab's Blog extension, many of them are especially designed for the use on our own templates - but can be used and implemented by any OctoberCMS user, of course. This documentation just describes the technical components and just shows a few small examples. Thus, you should be familiar with the default OctoberCMS behaviour and development before starting here.

However, the following list shows all available features as in Version 1.3.0:

- Blog Comments
	- Moderatable Post Comments (compatible with RainLab.User, not required)
	- Multi-Depth Replies (Supports nested or simple view)
	- Like / Dislike counter and Favourite switch
	- Honeypot and Captcha field (last is based on Gregwar Captcha)
	- Author and Favorite Highlights on the comment list
	- Access on any post model via `post.bloghub.comments` and `*.comments_count`
	- AJAX enabled environment (requires `{% framework %}` tag)
	- GDPR friendly - No IP or other sensitive data is stored in plain or recoverable way
- Blog Meta
	- Theme-related meta data can be set on the `theme.yaml`
	- Global meta data can be configured on the backend settings page
	- Meta Key based access via `post.bloghub.meta.[meta_key]`
- Blog Tags
	- Supports unique slug, title, description, promoted flag and color
	- Supports an own archive page using the `[bloghubTagArchive]` component
	- Access on the post via `post.bloghub.tags` or `post.bloghub.promoted_tags`
- Template Components
	- `[bloghubBase]` - Base settings, should be set on all related base layouts
	- `[bloghubPostsByAuthor]` - List posts by author	
	- `[bloghubPostsByCommentCount]` - List posts by comment counter	
	- `[bloghubPostsByDate]` - List posts by date
	- `[bloghubPostsByTag]` - List posts by tag
	- `[bloghubCommentList]` - List comments
	- `[bloghubCommentSection]` - Comment Section (List and Form) for single posts
	- `[bloghubTags]` - List or Cloud of tags 
	- Extends the sorting options of `blogPosts`
- Additional Menus
	- *coming soon*
- Additional Permissions
	- `ratmd.bloghub.comments`
	- `ratmd.bloghub.comments.access_comments_settings`
	- `ratmd.bloghub.comments.moderate_comments`
	- `ratmd.bloghub.comments.delete_coments`
	- `ratmd.bloghub.tags`
	- `ratmd.bloghub.tags.promoted`
- Extended Post Model
	- `post.bloghub.detail_meta_title` - Generated Meta title
	- `post.bloghub.detail_meta_description` - Generated Meta description
	- `post.bloghub.detail_read_time` - Estimated read time calculation
	- `post.bloghub.detail_published_ago` - "Published x y ago" string date/time string
	- `post.bloghub.tags` - Assigned tag collection
	- `post.bloghub.promoted_tags` - Assigned promoted tag collection
	- `post.bloghub.meta` - Assigned meta collection (also accessible with meta key)
	- `post.bloghub.comments` - Comment List (configurable)
	- `post.bloghub.comments_count` - Comment List Count (configurable)
	- `post.bloghub.views` - Views Counter
	- `post.bloghub.unique_views` - Visitor Counter
	- `post.bloghub.hasSeen` - Switch if current visitor has already seen this post
	- `post.bloghub.author` - Assigned post author (alias for `post.user`)
	- `post.bloghub.next` - Get next post (configurable)
	- `post.bloghub.previous` - Get previous post (configurable)
	- `post.bloghub.related` - Post List of related post (configurable)
	- `post.bloghub.random` - Post List of random posts (configurable)
- Extended User Model (accessible via `post.user` or `post.bloghub.author`)
	- `post.user.bloghub.url` - Full Author URL (required `bloghubAuthorPage`)
	- `post.user.bloghub.slug` - Author Slug (used in author archive pages)
	- `post.user.bloghub.display` - Generated display name (configurable)
	- `post.user.bloghub.count` - Counts posts (configurable)
- Dashboard Widgets
	- Comments List (shows the last and non-approved comments)
	- Posts Lists (shows the last and own posts)
	- Posts Statistics (shows some basic statistics about your posts)

## Blog Comments
Version 1.3.0 of our **BlogHub** plugin adds a new comment environment, including some more complex template components. The comment system is highly configurable and supports:

- Favorites (the author can favorite comments to highlight them)
- Likes / Dislikes (can also be restricted to logged in users)
- Reply (multi-depth enabled)
- Moderation (on the backend as well as frontend)
- Additional title and Markdown-enabled body
- Username and E-Mail address for not-logged-in users
- Terms of Service checkbox with link to a CMS or Static Page
- Honeypot and Catpcha field (using Gregwar Captcha) for guests only
- Hide on Dislike Ratio
- Single Post configuration (hide or close comments per post)

The comments system distinguish between guests, frontend users and backend users. Guests are required to fill out the username, email address, terms of service box and are controlled by the Honeypot and Captcha fields (when enabled). Logged-In Users can directly enter the comment title and body, are not controlled by any Honeypot or Captcha system and comments can also be set to be approved directly.

However, backend users are able to view and moderate pending comments on the single post page. Post authors do also have the ability to favorite comments, which pins or highlights them on the respective comments list. Of course, the moderation can also be done on October's backend page - as long as the respective permissions are set.

Comments can either be accessed directly on the post model, using `bloghub.comments` or `bloghub.comments_count`  or by using one of the available template components, as described below.

## Blog Meta
Each single blog post can be extended and enriched with additional meta fields, for example to provide specific data for the frontend templating or settig SEO-specific values. Meta fields can either be set on the template's `theme.yaml` file or using the "Custom Meta Fields" settings page on Octobers backend. The first solution is meant for template designers to provide "already supported" and implemented meta fields. The second one is especially designed for administrators to provide a general-available set of meta field (which must be implemented manually, of course).

However, the single meta values can be access on the respective post model using `post.bloghub.meta.[meta_key]`.

### Create Meta Fields via theme.yaml
Theme-related meta fields must be set in the template's `theme.yaml` file, using Octobers [Form Fields definition syntax](https://docs.octobercms.com/3.x/element/definitions.html):

```yaml
# Your Theme Data

ratmd.bloghub:
	post:
		# Your Meta Data
```

### Create Meta Fields via Backend
Global meta fields can be created on the "Custom Meta Fields" settings page. Here you can define a custom meta name, the respective meta type and te meta configuration... again using Octobers [Form Fields definition syntax](https://docs.octobercms.com/3.x/element/definitions.html).

## Blog Tags
BlogHub provides additional blog tags, which can be created and assigned on the fly on each blog post. However, tags can also be additionally customized by setting a promotion flag, title, description and color. All of those additional values CAN be used by template designers to highlight or describe your tags or tag archive pages even better.

The assigned Tags can either be accessed directly on the post model using `bloghub.tags` or by using one of the available template components, as described below. One of the components allows to build a tag archive page with support for multipe tag queries. When enabled you can show posts which must contain all tags (ex. `/blog/tag/tag1+tag2`) or which must contain either of the provided tags (ex. `/blog/tag/tag1,tag2`).

## Template Components
The **BlogHub** OctoberCMS plugin provides the following components.

### [bloghubBase]
The `[bloghubBase]` component SHOULD be set on all CMS layouts, which are or can be used to display the blog posts, since it provides the basic configuration for all bloghub and RainLab.Blog components (such as the single archive pages). When this component is not set, the default values (as shown below) will be used, when not otherwise changed.

This component does NOT provide a default template, since it is only be used to configure the exisiting and provided components only. Using `{% component 'bloghubBase' %}` will result in an error.

#### Component Arguments
This component provides the following arguments.

```ini
[bloghubBase]
archiveAuthor = 'blog/author'
archiveDate = 'blog/date'
archiveTag = 'blog/tag'

authorUseSlugOnly = 0
date404OnInvalid = 1
date404OnEmpty = 1
tagAllowMultiple = 0
```

##### Argument: `archiveAuthor`
Defines the CMS page, used for the author archives. You can set this argument to 0 to disable the author archive pages in general.

##### Argument: `archiveDate`
Defines the CMS page, used for the date archives. You can set this argument to 0 to disable the date archive pages in general.

##### Argument: `archiveTag`
Defines the CMS page, used for the tag archives. You can set this argument to 0 to disable the tag archive pages in general.

##### Argument: `authorUseSlugOnly`
When this argument is set to 1, the author archive page will only look up for the `author_slug` backend user column and skips checking the `login` column. 

##### Argument: `date404OnInvalid`
This argument allows to control if a 404 error should be thrown, when the passed date is invalid (for example: `2022-13-10`).

##### Argument: `date404OnEmpty`
This argument allows to control if a 404 error should be thrown, when the passed date does not contain any post. 

##### Argument: `tagAllowMultiple`
This argument allows to enable the usage of complex tag queries, which includes multiple tags. When enabled, you can combine multiple tags either by using a comma, to display posts that have "either" of the provided tags, or a plus to display posts that have "all" of them.

For example:
`http://domain.tld/blog/tag/tag1,tag2,tag3` - Posts must have AT LEAST ONE of the three provided tags (so either tag1, tag2 or tag3).

`http://domain.tld/blog/tag/tag1+tag2+tag3` - Posts must have ALL of the three provided tags (so tag1 AND tag2 AND tag3).

#### Page Variables
This component adds the following page variables.

##### Variable: `bloghub_config`
The `bloghub_config` variable contains all configured arguments as described above.

### [bloghubPostsByAuthor]
This component uses RainLab's `[blogPosts]` component as base class, thus all options below, except for `authorFilter` are exactly the same as on the referenced class. Check out RainLab's extension for more details about the single arguments.

#### Component Arguments
This component provides the following arguments.

```ini
[bloghubPostsByAuthor]
pageNumber = '{{ :page }}'
categoryFilter = 
postsPerPage = 10
noPostsMessage = '...'
sortOrder = 'published_at desc'
categoryPage = 'blog/category'
postPage = 'blog/post'
exceptPost = 
exceptCategories =
authorFilter = '{{ :slug }}'
```

##### Argument: `authorFilter`
The strict author slug or the desired URL parameter. The author slug is either the `author_slug`, when set on the respective backend user, or the `login` value (when `author_slug` is empty) unless it is disabled by the `[bloghubBase]` component as described above. 

**Attention:** Due to security aspects, it is highly recommended setting an author_slug on each backend user / author, which differs from the login name. Otherwise attackers could use the login name in bruteforce / rainbow table attacks.

#### Page Variables
This component adds the following page variables.

##### Variable: `author`
This component injects the `author` page variable to the page object, which points to the BackendUser model. The **BlogHub** plugin extends the BackendUser model with a few additional dynamic properties, check below at "Extended User Model" for more details.

### [bloghubPostsByCommentCount]
This component uses RainLab's `[blogPosts]` component as base class, thus all options below, and does not extend the existing arguments as on the references class. It only removed the `sortOrder` option and injects the own sorting method. Check out RainLab's extension for more details about the single arguments.

#### Component Arguments
This component does not provide any additional arguments, check out RainLab.Blog for more details about the existing arguments below.

```ini
[bloghubPostsByCommentCount]
pageNumber = '{{ :page }}'
categoryFilter = 
postsPerPage = 10
noPostsMessage = '...'
categoryPage = 'blog/category'
postPage = 'blog/post'
exceptPost = 
exceptCategories =
```

#### Page Variables
This component does not add an additional page variable.

### [bloghubPostsByDate]
This component uses RainLab's `[blogPosts]` component as base class, thus all options below, except for `dateFilter` are exactly the same as on the referenced class. Check out RainLab's extension for more details about the single arguments.

#### Component Arguments
This component provides the following arguments.

```ini
[bloghubPostsByDate]
pageNumber = '{{ :page }}'
categoryFilter = 
postsPerPage = 10
noPostsMessage = '...'
sortOrder = 'published_at desc'
categoryPage = 'blog/category'
postPage = 'blog/post'
exceptPost = 
exceptCatgexceptCategories =
dateFilter =  '{{ :date }}'
```

##### Argument: `dateFilter`
The direct date string (as described below) or the desired URL parameter. Invalid dates and empty date archives will throw an 404 error page, when not otherwise configured in `[bloghubBase]` (see above). Valid Date Formats:

Year archive: `Y` example: `https://domain.tld/blog/date/2022`
Month archive: `Y-m`, example: `https://domain.tld/blog/date/2022-01`
Week archive: `Y_W`, example: `https://domain.tld/blog/date/2022_2`
Day archive: `Y-m-d`, example: `https://domain.tld/blog/date/2022-01-01`

We recommend using the following URL parameter to get the year, month and day archive working. Of course, you can also use a more complex regular expression to evaluate the date string more precisely, but this would be unnecessary complex and even more unreadable.

```ini
url = "/blog/date/:date|^\d{4}(\-\d{2}(\-\d{2}))?/:page?"
```

The week archive can be added as follows (and does NOT require a leading zero);

```ini
url = "/blog/date/:date|^\d{4}(\-\d{2}(\-\d{2})|(\_\d{1,2}))?/:page?"
```

#### Page Variables
This component adds the following page variables.

##### Variable: `date`
The additional `date` page variable contains an array with the evaluated and sanitized date values. Depending on the archive it may contains `year`, `month`, `week` and / or `day`.

##### Variable: `dateType`
The `dateType` variable contains a simple string to clarify which archive page / date type has been detected from the passed URL parameter. It points either to `year`, `month`, `week` or `day`.

##### Variable: `dateFormat`
The `dateFormat` page variable contains a formatted date/time string, depending on the detected dateType evaluated from the passed URL parameter. The following list shows the format, used for the respective date types:

- dateType: `year` - format: `Y` - example: `2022`
- dateType: `month` - format: `F, Y` - example: `January, 2022`
- dateType: `week` - format: `\WW, >` - example: `W2, 2022`
- dateType: `day` - format: `F, d. Y` - example: `January, 17. 2022`

It is currently NOT possible to use own date/time formats for the single date types.

### [bloghubPostsByTag]
This component uses RainLab's `[blogPosts]` component as base class, thus all options below, except for `tagFilter` are exactly the same as on the referenced class. Check out RainLab's extension for more details about the single arguments.

#### Component Arguments
This component provides the following arguments.

```ini
[bloghubPostsByTag]
pageNumber = '{{ :page }}'
categoryFilter = 
postsPerPage = 10
noPostsMessage = '...'
sortOrder = 'published_at desc'
categoryPage = 'blog/category'
postPage = 'blog/post'
exceptPost = 
exceptCatgexceptCategories =
tagFilter = '{{ :tag }}'
```

##### Argument: `tagFilter`
The direct tag query string) or the desired URL parameter. When the `tagAllowMultiple` option is set, as described on the `[bloghubBase]` component, you can pass multiple tags using the `+` character (to include posts which contains ALL tags) as well as the `,` character (to include posts with contains at least one of the tags). If none of the listed tag slugs exists, the component will throw the 404 error page.

#### Page Variables
This component adds the following page variables.

##### Variable: `tag`
The single tag class model, when only one tag has been used for the generated archive page.

##### Variable: `tags`
The tag class models as array, when more then one tag has been used for the generated archive page. Keep in mind, that multiple-tag archive pages are only available, when the `tagAllowMultiple` option of the `[bloghubBase]` component has been set to true.

### [bloghubCommentList]
The `[bloghubCommentList]` displays a configurable list of comments.

#### Component Arguments
This component provides the following arguments.

```ini
[bloghub_CommentList]
postPage = 
excludePosts = 
amount = 5
sortOrder = 'published_at desc'
onlyFavorites = 0
hideOnDislikes = 0
```

##### Argument: `postPage`
The desired post page, used for the post URLs.

##### Argument: `excludePosts`
Allows to exclude one or more posts using their IDs or slugs (in a comma-separated way).

##### Argument: `amount`
The desired amount of comments to show.

##### Argument: `sortOrder`
Allows to change the sort order direction of the respective comments list. You can choose from the following values:

- `created_at DESC` - Shows the newest comments on top (default)
- `created_at ASC` - Shows the oldest comments on top
- `likes DESC` - Shows the most-liked comments on top
- `likes ASC` - Shows the most-liked comments on bottom
- `dislikes DESC` - Shows the most-disliked comments on top
- `dislikes ASC` - Shows the most-disliked comments on bottom

##### Argument: `onlyFavorites`
The post author is able to favourite comments, if not disabled via the BlogHub settings page. This option allows to only show favorite comments in the comments list.

##### Argument: `hideOnDislikes`
The `hideOnDislike` argument allows you to hide comments with the declared amount of dislikes or with a relative amount of dislikes compared to the likes, the default value `false` will disable this function completely. 

Example: Hide all comments with 10 dislikes or more:

```ini
[bloghubComments]
hideOnDislike = 10
```

Example: Hide all comments when the dislike counter is double as high as the like one:

```ini
[bloghubComments]
hideOnDislike = :2
```

Other example: `:4` (the dislike counter must be four times higher then the like counter to hide the respective comment).

#### Page Variables
This component adds the following page variables.

##### Variable: `comments`
The main collection with the configured amount of comments.

### [bloghubCommentSection]
The `[bloghubCommentSection]` component is used to show the comment list and a comment form on the single post CMS page. It is not supported to be used outside, even if it is possible by setting the respective arguments below. However, this has not been tested and may does not work as intended.

#### Component Arguments
This component provides the following arguments.

```ini
[bloghubCommentSection]
postSlug = 
commentsPerPage = 10
pageNumber = 
sortOrder = 'published_at desc'
commentHierarchy = 1
commentsAnchor = 'comments'
pinFavorites = 0
hideOnDislikes = 0
formPosition = 'above'
disableForm = 0
```

##### Argument: `postSlug`
Allows to pass a strict post slug or a URL parameter which is used to distinguish the current post slug from. When this value is empty (which is the default) the component will try to auto-detect the respective post slug using the `blogPost` argument and similar relations.

##### Argument: `commentsPerPage`
Allows to declare the number of comments to be shown per page. The default value is set to 10.

##### Argument: `pageNumber`
Allows to pass a strict page number or a URL parameter, which is used to distinguish the current page number from. When this value is empty (default), it will use and apply the `cpage` URL GET parameter as page nu,ber.

##### Argument: `sortOrder`
Allows to change the sort order direction of the respective comments list. You can choose from the following values:

- `created_at DESC` - Shows the newest comments on top (default)
- `created_at ASC` - Shows the oldest comments on top
- `likes DESC` - Shows the most-liked comments on top
- `likes ASC` - Shows the most-liked comments on bottom
- `dislikes DESC` - Shows the most-disliked comments on top
- `dislikes ASC` - Shows the most-disliked comments on bottom

##### Argument: `commntHierarchy`
Changes the way how the comment list is shown: A hierarchically list will display all replies below their parent comments - This option may highly increase the height of your size, because the `commentsPerPage` argument does NOT recognize the amount of children within the nested tree. Using the flat option (by disabling the `commntHierarchy` option) will display each comment in a single nested list using a "reply to" quotation above the comment content to show the relation of the respective reply.

##### Argument: `pinFavorites`
The post author is able to favourite comments, if not disabled via the BlogHub settings page. This option allows to pin favorite comments on top of the comments list and thus can be seen by everyone directly.

##### Argument: `hideOnDislikes`
The `hideOnDislike` argument allows you to hide comments with the declared amount of dislikes or with a relative amount of dislikes compared to the likes, the default value `false` will disable this function completely. 

Example: Hide all comments with 10 dislikes or more:

```ini
[bloghubComments]
hideOnDislike = 10
```

Example: Hide all comments when the dislike counter is double as high as the like one:

```ini
[bloghubComments]
hideOnDislike = :2
```

Other example: `:4` (the dislike counter must be four times higher then the like counter to hide the respective comment).

##### Argument: `formPosition`
Change the position of the comment form. You can either set the comment form `above` the comment list or `below`. The default value is set to `above`.

##### Argument: `disableForm`
This argument allows you to disable the comment form, regardless of the configuration set on the single post itself. This is useful for alternative post views, where it should not be possible to comment itself, but to still see the list of comments.

#### Page Variables
This component adds the following page variables.

##### Variable: `showComments`
A boolean state if the comment section / component should be displayed or not.

##### Variable: `showCommentsForm`
A boolean state if the comment form should be displaced or not.

##### Variable: `showCommentsHierarchical`
A boolean state pointing to the `commntHierarchy` component argument.

##### Variable: `comments`
The main comments collection to be used on the comments list.

##### Variable: `commentsFormPosition`
The comment form position (above or below) as declared in the component argument.

##### Variable: `commentsMode`
The comments mode, declared on the single post configuration and further evaluated on different options and states.

##### Variable: `currentUser`
The current user model or null if the current user is not logged in.

##### Variable: `currentUserIsGuest`
A boolean state if the current user is not logged in.

##### Variable: `currentUserIsFrontend`
A boolean state if the current user is a frontend user of the RainLab.User plugin.

##### Variable: `currentUserIsBackend`
A boolean state if the current user is a backend OctoberCMS user.

##### Variable: `isLoggedIn`
A boolean state if the current user is logged in (either on the frontend or backend authentication method).

##### Variable: `currentUserCanLike`
A boolean state if the current user can like comments, depending on the used configuration.

##### Variable: `currentUserCanDislike`
A boolean state if the current user can dislike comments, depending on the used configuration.

##### Variable: `currentUserCanFavorite`
A boolean state if the current user can favourite comments, depending on the used configuration and if the current user is the post author itself.

##### Variable: `currentUserCanComment`
A boolean state if the current user can write comments or replies, depending on the used configuration.

##### Variable: `currentUserCanModerate`
A boolean state if the current user can moderate pending comments, depending on the used configuration and if the current user is a backend OctoberCMS user.

##### Variable: `showCommentFormTitle`
A boolean state if the comment title form field should be visisble or not, depending on the used configuration.

##### Variable: `allowCommentFormMarkdown`
A boolean state if the comment content supports markdown or not, depending on the used configuration.

##### Variable: `showCommentFormTos`
A boolean state if the Terms of Service checkbox should be visible or not, depending on the used configuration.

##### Variable: `commentFormTosLabel`
The HTML label used for the Terms of Service checkbox, if enabled and visible.

##### Variable: `showCommentFormCaptcha`
A boolean state if the GREGWAR Captcha should be shown or not, depending on the used configuration and the current user.

##### Variable: `captchaImage`
The base64 encoded GREGWAR Captcha image, used when the cpatcha form field is enabled and shown to the current user.

##### Variable: `showCommentFormHoneypot`
A boolean state if the honeypot fields should be shown or not, depending on the used configurations. Wen enabled, the honeyport fields are also added to the form for frontend and bachend users.

##### Variable: `honeypotUser`
The honeypot username form field name, when the honeypot fields are enabled.

##### Variable: `honeypotEmail`
The honeypot email form field name, when the honeypot fields are enabled.

##### Variable: `honeypotTime`
The honeypot check-time valuem when the honeypot fields are enabled.

##### Variable: `validationTime`
A general form validation time, used together with the form validation hash to secure the comment form.

##### Variable: `validationHash`
A general form validation hash, used together with the form validation time to secure the comment form.

### [bloghubTags]
_descrption_

#### Component Arguments
This component provides the following arguments.

```ini
[bloghub_PostsByTags]
tagPage = 'blog/tag'
displayAs = 'list'
```

#### Page Variables
This component adds the following page variables.

## Additional Menus


## Additional Permissions
The **BlogHub** OctoberCMS plugin adds the following permissions.

### `ratmd.bloghub.comments`
Allows to access the `Comments` side menu on the RainLab.Blog main menu. The `Comments` menu contains access to the comments moderation. However, this permission is NOT required to access the BlogHub and BlogHub / Comments settings page, which requires the default RainLab permission: `rainlab.blog.manage_settings`.

### `ratmd.bloghub.comments.access_comments_settings`
This permissions allows to get access to the post-related comment configuration tab. This tab contains the options to change the visibility of the whole comment section as well as to change the comment mode for the respective post only.

### `ratmd.bloghub.comments.moderate_comments`
This permission allows to moderate comments on the backend as well as on the frontend page. The respective user can approve, reject or mark pending comments as spam. While the forntend does only allow to approve and reject pending comments, the backend also supports to change the comment status of any comment.

### `ratmd.bloghub.comments.delete_coments`
This permission allows to delete comments of any state and is an extension to the `moderate_comments` permission above.

### `ratmd.bloghub.tags`
This permission allows to access the `Tags` side menu on the RainLab.Blog main menu as well as the tags relation field on the single post backend page. The `Tags` menu contains the management environment for all available tags.

### `ratmd.bloghub.tags.promoted`
This permission allows to set the `Promote` flag on the single tags using the additional `Tags` menu under `Blog`. Since Promoted Tags are may shown different - depending on the theme - this flag has received his own permission additionally to the `tags` one as shown above.

## Extended Post Model
The **BlogHub** OctoberCMS plugin extends RainLab's Blog Post class model with the following additional properties and methods.

### `post.bloghub.detail_meta_title`
**Work in Progress** - This property returns the post title itself at the moment. However, we're working on additional SEO abilities and functions to be released in a future update.

### `post.bloghub.detail_meta_description
**Work in Progress** - This property returns the post escerpt or summary itself at the moment. However, we're working on additional SEO abilities and functions to be released in a future update. Stay tuned.

### `post.bloghub.detail_read_time`
Returns a formatted estimated read time calculation of the post content. You can either use it as a dynamic property, which returns `Read Time: x minutes, y seconds`, or as a function.

```twig
{# Formatted Property #}
{{ post.bloghub.detail_read_time }}

{# Custom Format #}
{% set read_time = post.bloghub.detail_read_time(null) %}
Estimated Read Time: {{ read_time.minutes * 60 + read_time.seconds }} seconds
```

### `post.bloghub.detail_published_ago`
The dynamic `detail_published_ago` property / method returns a time ago string instead of the default date/time stamp, using Carbon.

### `post.bloghub.tags`
Returns the tag collection assigned to the current post.

```twig
<ul>
	{% for tag in post.bloghub.tags %}
		<li><a href="{{ tag.url }}">{{ tag.title | default(tag.slug) }}</a></li>
	{% endfor %}
</ul>
```

### `post.bloghub.promoted_tags`
Returns the filtered tag collection assigned to the current post and contains only the tags with the promote flag set.

```twig
<ul>
	{% for tag in post.bloghub.promoted_tags %}
		<li><a href="{{ tag.url }}">{{ tag.title | default(tag.slug) }}</a></li>
	{% endfor %}
</ul>
```

### `post.bloghub.meta`
Returns the meta collection assigned to the current post. The collection has a name => value mapping, which allows you to receive the meta values by its related keys, as shown below.

```twig
{# Receive a specific meta value #}
<span>{{ post.bloghub.meta.simple_title }}</span>

{# Receive all meta pairs #}
<ul>
    {% for name, value in post.bloghub.meta %}
        <li>{{ name }}: {{ value | join(', ') }}</li>
    {% endfor %}
</ul>
```

### `post.bloghub.comments`
Returns the TreeCollection of the approved comments.

### `post.bloghub.comments_count`
Returns the number of the approved comments.

### `post.bloghub.views`
Returns the view counter of the current post.

### `post.bloghub.unique_views`
Returns the unique view counter of the current post.

### `post.bloghub.hasSeen`
Returns a boolean state, which indicates if the current user has already seen this post. 

**Attention:** At the moment, it doesn't make any sense using this property on the single post page itself, becuase the respective values will be set before the rendering of the page is done. Thus, this will always return true. We're currently working on a solution to set the desired value AFTER the page rendering, which allows you to check if the current user "visits" the blog page for the first time, or not.

### `post.bloghub.author`
Returns the BackendUser model of the current author and thus does exactly the same as `post.user`.

### `post.bloghub.next`
Returns the next blog post, which has been published BEFORE the current post model. Using this function as property will just return one single post without any further filter. However, you can also use `bloghub.next()` as method to apply additional rules.

```twig
{# Get next blog post #}
{{ post.bloghub.next }}

{# Get 3 next blog posts #}
{{ post.bloghub.next(3) }}

{# Get next blog post within the same categories #}
{{ post.bloghub.next(1, true) }}
```

### `post.bloghub.previous`
Returns the previous blog post, which has been published AFTER the current post model. Using this function as property will just return one single post without any further filter. However, you can also use `bloghub.previous()` as method to apply additional rules.

PS.: You can also use `bloghub.prev()`, which is an alias for `bloghub.previous()`.

```twig
{# Get previous blog post #}
{{ post.bloghub.prev }}

{# Get 3 previous blog posts #}
{{ post.bloghub.prev(3) }}

{# Get previous blog post within the same categories #}
{{ post.bloghub.prev(1, true) }}
```

### `post.bloghub.related`
Returns a collection of related blog posts, excluding the current post model. You can change the number of posts with the first parameter and you can exclude one or more posts by passing their IDs as second parameter.

### `post.bloghub.random`
Returns a collection of random blog posts, excluding the current post model. You can change the number of posts with the first parameter and you can exclude one or more posts by passing their IDs as second parameter.

## Extended User Model
The **BlogHub** OctoberCMS plugin extends the backend user class model with the following additional properties and methods. You can receive the author of the current post model using either `post.user` or the bloghub alias `post.bloghub.author`.

### `post.user.bloghub.url`
Returns the full author archive URL of the current user, as long as the author page is set using the `[bloghubBase]` component, as described above.

### `post.user.bloghub.slug`
Returns the plain author URL slug of the current user, which is either the `ratmd_bloghub_author_slug` column or the `login` name if the first is not set.

### `post.user.bloghub.display`
Returns the display name of the current user. The display name is either the `ratmd_bloghub_display_name` column, the `first_name last_name` columns or the titlized login name.

### `post.user.bloghub.about` 
Returns the `ratmd_bloghub_about_me` column of the current user.

### `post.user.bloghub.count`
Returns the number of published posts of the current user.

## Dashboard Widgets

