# BlogHub v1.4.0 - Documentation
Welcome to the BlogHub documentation page!

## Table of Contents
1. Requirements
2. Overview
3. Blog Comments
4. Blog Meta
5. Blog Tags
6. Template Components
7. Additional Menus
8. Additional Permissions
9. Extended Post Model
10. Extended User Model
11. Dashboard Widgets

## 1. Requirements
As of Version 1.4.0, support for OctoberCMS v2 has been discontinued (though it may still work).

**Required**
- OctoberCMS v3
- PHP 7.4+ or 8.0+
- [Gregwar/Captcha](https://github.com/Gregwar/Captcha)
- [RainLab.Blog](https://octobercms.com/plugin/rainlab-blog)

**Optional**
- [RainLab.User](https://octobercms.com/plugin/rainlab-user)
- [RainLab.Pages](https://octobercms.com/plugin/rainlab-pages)
- [RainLab.Translate](https://octobercms.com/plugin/rainlab-translate)

The optional plugins listed above extend existing features or provide additional functionalities for 
the **BlogHub** plugin but are not mandatory. The only required extension is **RainLab.Blog**.

## 2. Overview
Our **BlogHub** extension significantly enhances the **RainLab.Blog** extension with various 
functionalities. Many of these features are specifically designed for our own templates but can be 
utilized and implemented by any OctoberCMS user. This documentation focuses on the technical 
components and includes a few small examples. Familiarity with the default OctoberCMS behavior and 
development practices is recommended before diving in.

Below is a list of all available features in Version 1.4.0:

### 2.1. Blog Comments
- Moderatable post comments (compatible with RainLab.User, but not required)
- Multi-depth replies (supports nested or simple view)
- Like/dislike counters and a favorite switch
- Honeypot and Captcha field (the latter is based on Gregwar Captcha)
- Author and favorite highlights in the comment list
- Access any post model via `post.bloghub.comments` and `*.comments_count`
- AJAX-enabled environment (requires `{% framework %}` tag)
- GDPR-friendly – No IP or sensitive data is stored in plain or recoverable ways

### 2.2. Blog Meta
- Theme-related metadata can be set in `theme.yaml`
- Global metadata can be configured on the backend settings page
- Meta key-based access via `post.bloghub.meta.[meta_key]`

### 2.3. Blog Tags
- Supports unique slug, title, description, promoted flag, and color
- Supports a dedicated archive page using the `[bloghubTagArchive]` component
- Access tags via `post.bloghub.tags` or `post.bloghub.promoted_tags`

### 2.4. Template Components
- `[bloghubBase]` - Base settings; should be included in all related layouts
- `[bloghubPostsByAuthor]` - List posts by author
- `[bloghubPostsByCommentCount]` - List posts sorted by comment count
- `[bloghubPostsByDate]` - List posts by date
- `[bloghubPostsByTag]` - List posts by tag
- `[bloghubCommentList]` - List of comments
- `[bloghubCommentSection]` - Comment section (list and form) for single posts
- `[bloghubTags]` - List or cloud of tags 
- Extends sorting options for `blogPosts`

### 2.5. Additional Menus
- *Coming soon*

### 2.6. Additional Permissions
- `ratmd.bloghub.comments`
- `ratmd.bloghub.comments.access_comments_settings`
- `ratmd.bloghub.comments.moderate_comments`
- `ratmd.bloghub.comments.delete_comments`
- `ratmd.bloghub.tags`
- `ratmd.bloghub.tags.promoted`

### 2.7. Extended Post Model
- `post.bloghub.detail_meta_title` - Generated meta title
- `post.bloghub.detail_meta_description` - Generated meta description
- `post.bloghub.detail_read_time` - Estimated read time calculation
- `post.bloghub.detail_published_ago` - "Published x y ago" date/time string
- `post.bloghub.tags` - Assigned tag collection
- `post.bloghub.promoted_tags` - Assigned promoted tag collection
- `post.bloghub.meta` - Assigned meta collection (accessible with meta key)
- `post.bloghub.comments` - Comment list (configurable)
- `post.bloghub.comments_count` - Comment list count (configurable)
- `post.bloghub.views` - Views counter
- `post.bloghub.unique_views` - Visitor counter
- `post.bloghub.hasSeen` - Indicates if the current visitor has already seen this post
- `post.bloghub.author` - Assigned post author (alias for `post.user`)
- `post.bloghub.next` - Retrieve the next post (configurable)
- `post.bloghub.previous` - Retrieve the previous post (configurable)
- `post.bloghub.related` - List of related posts (configurable)
- `post.bloghub.random` - List of random posts (configurable)

### 2.8. Extended User Model (accessible via `post.user` or `post.bloghub.author`)
- `post.user.bloghub.url` - Full author URL (requires `bloghubAuthorPage`)
- `post.user.bloghub.slug` - Author slug (used in author archive pages)
- `post.user.bloghub.display` - Generated display name (configurable)
- `post.user.bloghub.count` - Post count (configurable)

### 2.9. Dashboard Widgets
- Comments list (shows the latest and unapproved comments)
- Posts list (shows the latest posts by the author)
- Posts statistics (provides basic statistics about your posts)

## 3. Blog Comments
Version 1.3.0 of our **BlogHub** plugin introduced a new comment environment with enhanced template 
components. The comment system is highly configurable and supports the following features:

- **Favorites**: Authors can favorite comments to highlight them.
- **Likes / Dislikes**: Users can express their opinions, with restrictions for logged-in users.
- **Replies**: Supports multi-depth replies for threaded discussions.
- **Moderation**: Comments can be moderated both in the backend and frontend.
- **Customizable Fields**: Additional title and Markdown-enabled body for comments.
- **Guest Information**: Guests must provide a username and email address.
- **Terms of Service**: A checkbox with a link to a CMS or static page for agreement.
- **Honeypot and Captcha**: Optional fields (using Gregwar Captcha) to prevent spam, applicable for guests only.
- **Hide on Dislike Ratio**: Option to hide comments based on dislike ratios.
- **Single Post Configuration**: Option to hide or close comments on a per-post basis.

The comments system distinguishes between guests, frontend users, and backend users:

- **Guests**: Required to fill out the username, email address, and terms of service checkbox. 
They are subject to the Honeypot and Captcha fields when enabled.
- **Logged-In Users**: Can enter comments directly without the need for Honeypot or Captcha. Their 
comments can also be set to be approved automatically.
- **Backend Users**: Have the ability to view and moderate pending comments on the single post page. 
Post authors can favorite comments, which pins or highlights them in the comments list. Moderation 
can also be conducted through October's backend interface, provided the necessary permissions are 
granted.

Comments can be accessed directly on the post model using `bloghub.comments` or 
`bloghub.comments_count`, or by utilizing one of the available template components, as described 
below.

## 4. Blog Meta
Each blog post can be enriched with additional meta fields to provide specific data for frontend 
templating or to set SEO-related values. Meta fields can be defined in two ways:

1. **Template's `theme.yaml` File**: This method is intended for template designers to provide 
pre-defined and implemented meta fields.
2. **Custom Meta Fields Settings Page**: This option is designed for administrators to define a 
universally available set of meta fields, which must be implemented manually.

### 4.1. Create Meta Fields via `theme.yaml`
To create theme-related meta fields, you must define them in the template's `theme.yaml` file using 
October's [Form Fields definition syntax](https://docs.octobercms.com/3.x/element/definitions.html):

```yaml
# Your Theme Data

ratmd.bloghub:
  post:
    # Your Meta Data
```

### 4.2. Create Meta Fields via Backend
Global meta fields can be created on the "Custom Meta Fields" settings page. Here, you can define a 
custom meta name, specify the respective meta type, and configure the meta settings, again using 
October's [Form Fields definition syntax](https://docs.octobercms.com/3.x/element/definitions.html).

## 5. Blog Tags
The **BlogHub** extension offers enhanced functionality for blog tags, allowing you to create and 
assign tags dynamically to each blog post. Additionally, tags can be customized with various 
attributes, including:

- **Promotion Flag**: Indicates whether a tag is promoted.
- **Title**: A descriptive title for the tag.
- **Description**: A brief description to provide more context.
- **Color**: A color that visually represents the tag.

These attributes can be utilized by template designers to effectively highlight or describe tags and 
tag archive pages.

Assigned tags can be accessed directly on the post model using `bloghub.tags` or through one of the 
available template components described below. One of these components enables the creation of a tag 
archive page that supports multiple tag queries. When this feature is enabled, you can display posts 
that must contain all specified tags (e.g., `/blog/tag/tag1+tag2`) or posts that must contain any of
the provided tags (e.g., `/blog/tag/tag1,tag2`).

## 6. Template Components
The **BlogHub** OctoberCMS plugin provides a range of useful components for enhancing your blog 
functionality.

### 6.1. `[bloghubBase]`
The `[bloghubBase]` component **should** be included in all CMS layouts that display blog posts. 
This component provides the basic configuration for all BlogHub and RainLab.Blog components, 
including single archive pages. If this component is not set, default values (as listed below) will 
be used unless specified otherwise.

**Note**: This component does not provide a default template, as it is intended solely for 
configuring existing components. Attempting to use `{% component 'bloghubBase' %}` without proper 
setup will result in an error.

#### Component Arguments
The `[bloghubBase]` component accepts the following arguments: 

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
Defines the CMS page used for author archives. Set this argument to `0` to disable author archive 
pages entirely.

##### Argument: `archiveDate`
Defines the CMS page used for date archives. Set this argument to `0` to disable date archive pages 
entirely.

##### Argument: `archiveTag`
Defines the CMS page used for tag archives. Set this argument to `0` to disable tag archive pages 
entirely.

##### Argument: `authorUseSlugOnly`
When set to `1`, the author archive page will only check the `author_slug` backend user column and 
skip the `login` column.

##### Argument: `date404OnInvalid`
Controls whether a 404 error should be thrown when an invalid date is passed (e.g., `2022-13-10`).

##### Argument: `date404OnEmpty`
Controls whether a 404 error should be thrown when a passed date contains no posts.

##### Argument: `tagAllowMultiple`
Enables the use of complex tag queries that involve multiple tags. When enabled, you can combine 
multiple tags using a comma to display posts that have **either** of the provided tags, or a plus to 
display posts that have **all** of them.

**Examples**:
- `http://domain.tld/blog/tag/tag1,tag2,tag3` - Posts must have **at least one** of the specified 
tags (either `tag1`, `tag2`, or `tag3`).
- `http://domain.tld/blog/tag/tag1+tag2+tag3` - Posts must have **all** of the specified tags 
(i.e., `tag1 AND tag2 AND tag3`).

#### Page Variables
This component adds the following page variable:

##### Variable: `bloghub_config`
The `bloghub_config` variable contains all configured arguments as described above.

### 6.2. `[bloghubPostsByAuthor]`
The `[bloghubPostsByAuthor]` component builds upon RainLab's `[blogPosts]` component as its base 
class. Therefore, all options listed below, except for `authorFilter`, are identical to those in the 
referenced class. For detailed information about the individual arguments, please refer to the 
RainLab extension documentation.

#### Component Arguments
This component provides the following arguments:

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
The `authorFilter` argument specifies the strict author slug or the desired URL parameter. The 
author slug can either be the `author_slug` set on the respective backend user or the `login` value 
(if `author_slug` is empty). However, this behavior can be disabled by the `[bloghubBase]` component, 
as described previously.

**Note**: For security reasons, it is highly recommended to set a unique `author_slug` for each 
backend user/author that differs from the login name. Failing to do so could expose the login name 
to brute-force or rainbow table attacks.

#### Page Variables
This component adds the following page variable:

##### Variable: `author`
The `author` page variable is injected into the page object and points to the BackendUser model. The 
**BlogHub** plugin extends the BackendUser model with several additional dynamic properties. For 
more details, see the "10. Extended User Model" section below.

### 6.3. `[bloghubPostsByCommentCount]`
The `[bloghubPostsByCommentCount]` component is based on RainLab's `[blogPosts]` component. Unlike 
the referenced class, this component does not extend the existing arguments; instead, it removes the 
`sortOrder` option and implements its own sorting method. For detailed information about the 
available arguments, please refer to the RainLab extension documentation.

#### Component Arguments
This component does not introduce any additional arguments. Below are the existing arguments 
inherited from the `[blogPosts]` component:

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
This component does not add any additional page variables.

### 6.4. `[bloghubPostsByDate]`
The `[bloghubPostsByDate]` component is based on RainLab's `[blogPosts]` component, meaning that all 
options, except for `dateFilter`, are identical to those in the referenced class. For detailed 
information about the individual arguments, please refer to the RainLab extension documentation.

#### Component Arguments
This component provides the following arguments:

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
exceptCategories =
dateFilter = '{{ :date }}'
```

##### Argument: `dateFilter`

This argument accepts a direct date string (as described below) or a desired URL parameter. If an 
invalid date or an empty date archive is encountered, a 404 error page will be displayed unless 
configured otherwise in `[bloghubBase]` (see above).

**Valid Date Formats:**

- **Year archive:** `Y` (e.g., `https://domain.tld/blog/date/2022`)
- **Month archive:** `Y-m` (e.g., `https://domain.tld/blog/date/2022-01`)
- **Week archive:** `Y_W` (e.g., `https://domain.tld/blog/date/2022_2`)
- **Day archive:** `Y-m-d` (e.g., `https://domain.tld/blog/date/2022-01-01`)

We recommend using the following URL parameter to get the year, month, and day archives working 
smoothly. While you can employ a more complex regular expression to evaluate the date string more 
precisely, this might unnecessarily complicate the implementation:

```ini
url = "/blog/date/:date|^\d{4}(\-\d{2}(\-\d{2}))?/:page?"
```

For the week archive, you can use the following configuration (note that a leading zero is not 
required):

```ini
url = "/blog/date/:date|^\d{4}(\-\d{2}(\-\d{2})|(\_\d{1,2}))?/:page?"
```

#### Page Variables
This component adds the following page variables:

##### Variable: `date`
The `date` variable is an array containing the evaluated and sanitized date values. Depending on the 
archive type, it may include `year`, `month`, `week`, and/or `day`.

##### Variable: `dateType`
The `dateType` variable is a simple string indicating which archive page or date type has been 
detected from the passed URL parameter. It will point to either `year`, `month`, `week`, or `day`.

##### Variable: `dateFormat`
The `dateFormat` variable contains a formatted date/time string based on the detected `dateType` 
evaluated from the passed URL parameter. The following formats are used for the respective date 
types:

- **dateType: `year`** - format: `Y` (e.g., `2022`)
- **dateType: `month`** - format: `F, Y` (e.g., `January, 2022`)
- **dateType: `week`** - format: `\WW, Y` (e.g., `W2, 2022`)
- **dateType: `day`** - format: `F, d. Y` (e.g., `January, 17. 2022`)

Please note that it is currently NOT possible to use custom date/time formats for the individual 
date types.

### 6.5. `[bloghubPostsByTag]`
The `[bloghubPostsByTag]` component is built on RainLab's `[blogPosts]` component, meaning that all 
options, except for `tagFilter`, are identical to those in the referenced class. For detailed 
information about the individual arguments, please refer to the RainLab extension documentation.

#### Component Arguments
This component provides the following arguments:

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
exceptCategories =
tagFilter = '{{ :tag }}'
```

##### Argument: `tagFilter`
This argument accepts a direct tag query string or the desired URL parameter. When the 
`tagAllowMultiple` option is enabled in the `[bloghubBase]` component, you can pass multiple tags 
using the `+` character to include posts containing **all** specified tags, or the `,` character to 
include posts containing **at least one** of the tags. If none of the provided tag slugs exist, the 
component will return a 404 error page.

#### Page Variables
This component adds the following page variables:

##### Variable: `tag`
The `tag` variable contains the single tag class model when only one tag has been used for the 
generated archive page.

##### Variable: `tags`
The `tags` variable is an array of tag class models when more than one tag has been used for the 
generated archive page. Please note that multiple-tag archive pages are only available if the 
`tagAllowMultiple` option in the `[bloghubBase]` component has been set to true.

### 6.6. `[bloghubCommentList]`
The `[bloghubCommentList]` component displays a configurable list of comments.

#### Component Arguments
This component provides the following arguments:

```ini
[bloghubCommentList]
postPage = 
excludePosts = 
amount = 5
sortOrder = 'published_at desc'
onlyFavorites = 0
hideOnDislikes = 0
```

##### Argument: `postPage`
Specifies the desired post page, which is used for generating the post URLs.

##### Argument: `excludePosts`
Allows you to exclude one or more posts by their IDs or slugs, listed in a comma-separated format.

##### Argument: `amount`
Determines the number of comments to display.

##### Argument: `sortOrder`
Controls the sort order of the comments list. You can choose from the following values:

- `created_at DESC`: Shows the newest comments at the top (default).
- `created_at ASC`: Shows the oldest comments at the top.
- `likes DESC`: Shows the most-liked comments at the top.
- `likes ASC`: Shows the least-liked comments at the bottom.
- `dislikes DESC`: Shows the most-disliked comments at the top.
- `dislikes ASC`: Shows the least-disliked comments at the bottom.

##### Argument: `onlyFavorites`
When enabled, this option allows only favorite comments to be displayed in the list. The post author 
can favorite comments unless this feature is disabled via the BlogHub settings page.

##### Argument: `hideOnDislikes`
The `hideOnDislikes` argument lets you hide comments based on their dislike count. The default value 
is `false`, which disables this function entirely.

**Examples:**

- Hide all comments with 10 dislikes or more:
```ini
[bloghubCommentList]
hideOnDislikes = 10
```

- Hide all comments when the dislike count is double that of the like count:
```ini
[bloghubCommentList]
hideOnDislikes = :2
```

- Hide all comments when the dislike count is four times higher than the like count:
```ini
[bloghubCommentList]
hideOnDislikes = :4
```

#### Page Variables
This component adds the following page variable:

##### Variable: `comments`
The `comments` variable contains the main collection of the configured amount of comments.

### 6.7. `[bloghubCommentSection]`
The `[bloghubCommentSection]` component displays a comment list and a comment form on the single 
post CMS page. Although it is technically possible to use this component outside of its intended 
context by setting the respective arguments, this usage is not supported, untested, and may not 
function as expected.

#### Component Arguments
This component provides the following arguments:

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
Allows you to pass a strict post slug or a URL parameter to distinguish the current post slug. If 
this value is empty (the default), the component will attempt to auto-detect the respective post 
slug using the `blogPost` argument and similar relationships.

##### Argument: `commentsPerPage`
Specifies the number of comments to show per page. The default value is set to 10.

##### Argument: `pageNumber`
Allows you to pass a strict page number or a URL parameter to determine the current page number. If 
this value is empty (default), it will use the `cpage` URL GET parameter as the page number.

##### Argument: `sortOrder`
Controls the sort order of the comments list. You can choose from the following values:

- `created_at DESC`: Shows the newest comments at the top (default).
- `created_at ASC`: Shows the oldest comments at the top.
- `likes DESC`: Shows the most-liked comments at the top.
- `likes ASC`: Shows the least-liked comments at the bottom.
- `dislikes DESC`: Shows the most-disliked comments at the top.
- `dislikes ASC`: Shows the least-disliked comments at the bottom.

##### Argument: `commentHierarchy`
Controls how the comment list is displayed. When enabled, replies are shown below their parent 
comments in a hierarchical structure. This option may significantly increase the height of the 
comment section, as the `commentsPerPage` argument does **not** account for nested comments. 
Disabling this option will display each comment in a single nested list with a "reply to" quotation 
above the comment content.

##### Argument: `pinFavorites`
Allows the post author to pin favorite comments at the top of the comments list, ensuring they are 
visible to all users. This feature can be disabled via the BlogHub settings page.

##### Argument: `hideOnDislikes`
This argument allows you to hide comments based on a specified number of dislikes or a relative dislike-to-like ratio. The default value is `false`, disabling this feature entirely.

**Examples:**

- Hide all comments with 10 dislikes or more:
```ini
[bloghubCommentSection]
hideOnDislikes = 10
```

- Hide comments when the dislike count is double that of the like count:
```ini
[bloghubCommentSection]
hideOnDislikes = :2
```

- Hide comments when the dislike count is four times higher than the like count:
```ini
[bloghubCommentSection]
hideOnDislikes = :4
```

##### Argument: `formPosition`
Sets the position of the comment form. You can place the comment form either `above` or `below` the 
comment list. The default value is `above`.

##### Argument: `disableForm`
Disables the comment form, regardless of the configuration set on the single post. This is useful 
for alternative post views where commenting is not permitted, but the list of comments should still 
be visible.

#### Page Variables
This component adds the following page variables:

##### Variable: `showComments`
A boolean indicating whether the comment section should be displayed.

##### Variable: `showCommentsForm`
A boolean indicating whether the comment form should be displayed.

##### Variable: `showCommentsHierarchical`
A boolean indicating the state of the `commentHierarchy` argument.

##### Variable: `comments`
The main collection of comments used in the comments list.

##### Variable: `commentsFormPosition`
Indicates the position of the comment form (above or below), as declared in the component argument.

##### Variable: `commentsMode`
Represents the comments mode, as configured in the single post settings and evaluated across various 
options and states.

##### Variable: `currentUser`
The current user model, or `null` if the current user is not logged in.

##### Variable: `currentUserIsGuest`
A boolean indicating if the current user is not logged in.

##### Variable: `currentUserIsFrontend`
A boolean indicating if the current user is a frontend user of the RainLab.User plugin.

##### Variable: `currentUserIsBackend`
A boolean indicating if the current user is a backend OctoberCMS user.

##### Variable: `isLoggedIn`
A boolean indicating if the current user is logged in, regardless of frontend or backend 
authentication.

##### Variable: `currentUserCanLike`
A boolean indicating if the current user can like comments, based on the configuration.

##### Variable: `currentUserCanDislike`
A boolean indicating if the current user can dislike comments, based on the configuration.

##### Variable: `currentUserCanFavorite`
A boolean indicating if the current user can favorite comments, depending on their role and if they 
are the post author.

##### Variable: `currentUserCanComment`
A boolean indicating if the current user can write comments or replies, based on the configuration.

##### Variable: `currentUserCanModerate`
A boolean indicating if the current user can moderate pending comments, based on their role and the 
configuration.

##### Variable: `showCommentFormTitle`
A boolean indicating if the comment title field should be visible, depending on the configuration.

##### Variable: `allowCommentFormMarkdown`
A boolean indicating if the comment content supports Markdown formatting, based on the configuration.

##### Variable: `showCommentFormTos`
A boolean indicating if the Terms of Service checkbox should be visible, depending on the 
configuration.

##### Variable: `commentFormTosLabel`
The HTML label used for the Terms of Service checkbox, if enabled and visible.

##### Variable: `showCommentFormCaptcha`
A boolean indicating if the GREGWAR Captcha should be displayed, depending on the configuration and 
the current user.

##### Variable: `captchaImage`
The base64-encoded GREGWAR Captcha image, used when the captcha field is enabled.

##### Variable: `showCommentFormHoneypot`
A boolean indicating if the honeypot fields should be shown, depending on the configuration. If
enabled, honeypot fields are added to the form for both frontend and backend users.

##### Variable: `honeypotUser`
The honeypot username form field name when honeypot fields are enabled.

##### Variable: `honeypotEmail`
The honeypot email form field name when honeypot fields are enabled.

##### Variable: `honeypotTime`
The honeypot check-time value when honeypot fields are enabled.

##### Variable: `validationTime`
A general form validation time, used alongside the validation hash to secure the comment form.

##### Variable: `validationHash`
A general form validation hash, used in conjunction with the validation time to secure the comment form.

### 6.8. `[bloghubTags]`
The `[bloghubTags]` component is designed to display a list of tags associated with blog posts. This 
component enhances the user experience by allowing visitors to easily navigate through topics of 
interest, improving content discoverability on your blog.

#### Component Arguments
This component provides the following arguments:

```ini
[bloghubTags]
tagPage = 'blog/tag'
onlyPromoted = false
amount = 5
```

##### Argument: `onlyPromoted`
A boolean value that determines whether to display only promoted tags. If set to `true`, only tags 
marked as promoted will be shown. The default value is `false`, which means all tags will be 
displayed.

##### Argument: `amount`
Defines the number of tags to display. The default value is `5`, but this can be adjusted to show 
more or fewer tags based on your preference.

#### Page Variables
This component adds the following page variables:

##### Variable: `tags`
An array of tag models available on the site, allowing access to the individual tags and their 
respective properties.

## 7. Additional Menus
_Coming Soon_

## 8. Additional Permissions
The **BlogHub** OctoberCMS plugin adds the following permissions to enhance the management of 
omments and tags within the blog:

### 8.1. `ratmd.bloghub.comments`
This permission allows access to the **Comments** side menu within the RainLab.Blog main menu. 
The **Comments** menu includes access to comment moderation features. Note that this permission 
is **not required** for accessing the BlogHub and BlogHub / Comments settings page, which require 
the default RainLab permission: `rainlab.blog.manage_settings`.

### 8.2. `ratmd.bloghub.comments.access_comments_settings`
This permission grants access to the post-related comment configuration tab. This tab contains 
options to change the visibility of the entire comment section as well as to modify the comment mode 
for individual posts.

### 8.3. `ratmd.bloghub.comments.moderate_comments`
Users with this permission can moderate comments both in the backend and on the frontend. This 
includes the ability to approve, reject, or mark pending comments as spam. Note that while frontend 
moderation allows only the approval and rejection of pending comments, the backend offers additional 
capabilities, including changing the status of any comment.

### 8.4. `ratmd.bloghub.comments.delete_comments`
This permission allows users to delete comments of any status. It extends the `moderate_comments` 
permission mentioned above, providing full control over comment deletion.

### 8.5. `ratmd.bloghub.tags`
This permission allows access to the **Tags** side menu within the RainLab.Blog main menu, as well 
as access to the tags relation field on the single post backend page. The **Tags** menu contains a 
management environment for all available tags.

### 8.6. `ratmd.bloghub.tags.promoted`
Users with this permission can set the **Promote** flag on individual tags using the additional 
**Tags** menu under **Blog**. Promoted tags may be displayed differently depending on the theme, 
so this flag has its own distinct permission in addition to the general `tags` permission described 
above.

## 9. Extended Post Model
The **BlogHub** OctoberCMS plugin enhances RainLab's Blog Post class model with additional 
properties and methods for better SEO, readability, and user engagement.

### 9.1. `post.bloghub.detail_meta_title`
**Work in Progress** - Currently, this property returns the post title. Future updates will 
introduce enhanced SEO capabilities.

### 9.2. `post.bloghub.detail_meta_description`
**Work in Progress** - This property returns the post excerpt or summary. Additional SEO 
functionalities are planned for a future release.

### 9.3. `post.bloghub.detail_read_time`
Returns a formatted estimated read time for the post content. This can be used as a dynamic 
property or as a function.

```twig
{# Formatted Property #}
{{ post.bloghub.detail_read_time }}

{# Custom Format #}
{% set read_time = post.bloghub.detail_read_time(null) %}
Estimated Read Time: {{ read_time.minutes * 60 + read_time.seconds }} seconds
```

### 9.4. `post.bloghub.detail_published_ago`
Returns a "time ago" string using Carbon instead of the default date/time stamp.

### 9.5. `post.bloghub.tags`
Returns the collection of tags assigned to the current post.

```twig
<ul>
    {% for tag in post.bloghub.tags %}
        <li><a href="{{ tag.url }}">{{ tag.title | default(tag.slug) }}</a></li>
    {% endfor %}
</ul>
```

### 9.6. `post.bloghub.promoted_tags`
Returns a filtered collection of tags assigned to the current post that have the promote flag set.

```twig
<ul>
    {% for tag in post.bloghub.promoted_tags %}
        <li><a href="{{ tag.url }}">{{ tag.title | default(tag.slug) }}</a></li>
    {% endfor %}
</ul>
```

### 9.7. `post.bloghub.meta`
Returns the meta collection assigned to the current post, structured as name => value mappings.

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

### 9.8. `post.bloghub.comments`
Returns a TreeCollection of approved comments for the current post.

### 9.9. `post.bloghub.comments_count`
Returns the total number of approved comments for the current post.

### 9.10. `post.bloghub.views`
Returns the view count of the current post.

### 9.11. `post.bloghub.unique_views`
Returns the unique view count of the current post.

### 9.12. `post.bloghub.hasSeen`
Returns a boolean indicating if the current user has already seen this post.

**Note:** Using this property on the single post page may not yield accurate results since the 
values are set before page rendering. We're working on a solution to set the value after rendering 
to track first-time visits accurately.

### 9.13. `post.bloghub.author`
Returns the BackendUser model of the current author, identical to `post.user`.

### 9.14. `post.bloghub.next`
Returns the next published blog post before the current one. Use as a property to get a single post 
or as a method for additional rules.

```twig
{# Get next blog post #}
{{ post.bloghub.next }}

{# Get 3 next blog posts #}
{{ post.bloghub.next(3) }}

{# Get next blog post within the same categories #}
{{ post.bloghub.next(1, true) }}
```

### 9.15. `post.bloghub.previous`
Returns the previous published blog post after the current one. Use as a property to get a single 
post or as a method for additional rules.

**Note:** `bloghub.prev()` is an alias for `bloghub.previous()`.

```twig
{# Get previous blog post #}
{{ post.bloghub.prev }}

{# Get 3 previous blog posts #}
{{ post.bloghub.prev(3) }}

{# Get previous blog post within the same categories #}
{{ post.bloghub.prev(1, true) }}
```

### 9.16. `post.bloghub.related`
Returns a collection of related blog posts, excluding the current one. Adjust the number of posts 
and exclude specific posts by passing their IDs.

### 9.17. `post.bloghub.random`
Returns a collection of random blog posts, excluding the current one. Adjust the number of posts 
and exclude specific posts by passing their IDs.

## 10. Extended User Model
The **BlogHub** OctoberCMS plugin enhances the backend user class model with additional properties 
and methods. You can retrieve the author of the current post model using either `post.user` or the 
BlogHub alias `post.bloghub.author`.

### 10.1. `post.user.bloghub.url`
Returns the full author archive URL for the current user, provided the author page is set using the 
`[bloghubBase]` component as described in the documentation.

### 10.2. `post.user.bloghub.slug`
Returns the plain author URL slug for the current user. This can either be derived from the 
`ratmd_bloghub_author_slug` column or fall back to the `login` name if the former is not set.

### 10.3. `post.user.bloghub.display`
Returns the display name of the current user. The display name is determined in the following order:

- From the `ratmd_bloghub_display_name` column
- From a combination of `first_name` and `last_name` columns
- As a titlized version of the login name if none of the above are set.

### 10.4. `post.user.bloghub.about`
Returns the content of the `ratmd_bloghub_about_me` column for the current user. This provides a 
brief biography or description of the author.

### 10.5. `post.user.bloghub.count`
Returns the number of published posts authored by the current user. This helps in displaying author 
statistics and engagement metrics.

## 11. Dashboard Widgets
The **BlogHub** OctoberCMS plugin provides the following 3 backend dashboard widgets.

### 11.1. Comments List
The **Comments List** dashboard widget displays the last 6 comments, allowing users to approve, 
reject, or mark pending comments as spam. It also includes overall counters for comment statuses.

#### Option: `postPage`
Sets the single post CMS page to be linked in the post title shown with each respective comment.

#### Option: `defaultTab`
Allows you to change the default open tab for the widget. Options include:

  - `Pending` (Default)
  - `Approved`
  - `Rejected`
  - `Mark as Spam`

### 11.2. Posts List
The **Posts List** dashboard widget shows the most recently published posts, along with details 
such as view counters, categories, and authors.

#### Option: `postPage`
Sets the single post CMS page to be linked in the post title shown within the widget.

#### Option: `amount`
Changes the number of posts displayed within this widget.

#### Option: `excludeCategories`
Excludes posts from specified categories. You can pass one or more comma-separated category IDs or 
slugs.

### 11.3. Posts Statistics
The **Posts Statistics** dashboard widget presents summarized information as graphs about your blog 
posts, enabling quick insights into the number of published posts within a specific date range, as 
well as metrics for views, unique views, likes, and dislikes.

#### Supported Date Ranges
- Last 7 days
- Last 14 days
- Last month
- Last 3 months
- Last 6 months

#### Option: `defaultDateRange`
Changes the default date range used to generate the statistics graphs. (Default: Last 7 days).
