BlogHub - Documentation
=======================

Add own Custom Meta Data
------------------------

You can manage your own additional Post meta data either by using the `theme.yaml` file of your 
current template (recommended for template designer), or by visiting the Meta Data administration 
menu under Settings `->` Blog `->` Custom Meta Data. The meta fields of the first method will only 
be visible as long as the current theme is activated, while the second one works independent of the 
theme but - however - must be implemented on your own of course.

You can access your custom meta data values by using the `bloghub_meta_data` or `bloghub_meta` 
Post model arguments. Look below for more details.


### Adding own meta fields via theme.yaml

Open the `theme.yaml` file of your current active template and add the following lines on the 
bottom of this file:

```yaml
ratmd.bloghub:
    post:
        # Your Fields
```

Replace `# Your Fields` with the field definitions as described in the [official documentation](https://docs.octobercms.com/3.x/element/definitions.html).
Here is a small example of our NewsHub Premium OctoberCMS template:

```yaml
ratmd.bloghub:
    post:
        simple_subtitle:
            type: text
            label: theme.custom_meta.simple_subtitle.title
            comment: theme.custom_meta.simple_subtitle.comment
            span: left
        simple_title:
            type: text
            label: theme.custom_meta.simple_title.label
            comment: theme.custom_meta.simple_title.comment
            span: right
        layout:
            type: dropdown
            label: theme.custom_meta.post_layout.label
            comment: theme.custom_meta.post_layout.comment
            default: default
            showSearch: false
            options:
                default: theme.custom_meta.post_layout.default
                fullwidth: theme.custom_meta.post_layout.fullwidth
        #...
```


Additional Post Arguments
-------------------------

### post.bloghub_tags

This argument contains all `Tag` models as array, including all available values. You can use them 
in the same way as you would use `post.catgories`. Here is a small example:

```html
{% for tag in post.bloghub_tags %}
    {% if tag.promote %}
        <a href="{{ tag.url }}" title="Tag Archive: {{ tag.title | default(tag.slug) }}">
            <span class="post-tag post-tag-promoted"{{ tag.color ? ' style="background-color: {{ tag.color }}"' : '' }}>
                {{ tag.title | default(tag.slug) }}
            </span>
        </a>
    {% else %}
        <span class="post-tag">
            {{ tag.title | default(tag.slug) }}
        </span>
    {% endif %}
{% endfor %}
```


### post.bloghub_meta

Similar to `post.bloghub_tags` this argument contains all meta data Model objects as an array. We 
highly recommend using `post.bloghub_meta_data` (see below) to access your custom meta data in a 
more native way (using the meta name).


### post.bloghub_meta_data

The `post.bloghub_meta_data` argument contains the \[name\]: \[value\] pairs of all assigned custom Post 
meta data (even if the value is empty). Here is a small example:

```html
<div class="post-title">
    {{ post.bloghub_meta_data.special_title | default(post.title) }}
</div>
```

Keep in mind that the received \[value\] can also be an array!


Dynamic Post Methods
--------------------

### post.bloghub_similar_posts($limit = 3, exclude = [])

This dynamic Post method returns related or similar posts depending on the category and assigned 
tags. You can configure the amount of posts with the first parameter, the second one can be used to 
exclude other post ids next to the current one.


### post.bloghub_random_posts($limit = 3, exclude = [])

This dynamic Post method returns some random posts, excluding the current one. You can configure 
the amount of posts with the first parameter, the second one can be used to exclude other post ids 
next to the current one.


### post.bloghub_next_post()

This dynamic Post method returns the next post (according to the `published_at` date/time string) 
regardless of the category, or Null when the user is as the latest published one. It can be used to 
create a Next / Prev Post button on the single post.


### post.bloghub_next_post_in_category()

This dynamic Post method returns the next post (according to the `published_at` date/time string) 
of the same category, or Null when the user is as the latest published one. It can be used to 
create a Next / Prev Post button on the single post.


### post.bloghub_prev_post()

This dynamic Post method returns the previous post (according to the `published_at` date/time string) 
regardless of the category, or Null when the user is as the latest published one. It can be used to 
create a Next / Prev Post button on the single post.


### post.bloghub_prev_post_in_category()

This dynamic Post method returns the previous post (according to the `published_at` date/time string) 
of the same category, or Null when the user is as the latest published one. It can be used to 
create a Next / Prev Post button on the single post.


Template Components
-------------------

We highly recommend reading the documentation for the official [RainLab.Blog plugin](https://octobercms.com/plugin/rainlab-blog),
since the following components are based on the provided `blogPosts` component and thus work almost 
similar with the following differences:


### bloghubAuthorArchive

The `bloghubAuthorArchive` component provides an additional tag-based archive. It works similar to the 
Post list page, as described on the [RainLab.Blog plugin](https://octobercms.com/plugin/rainlab-blog),
but supports one additional parameter: `authorFilter` (which is usually set to `{{ :slug }}`).


### bloghubDateArchive

The `bloghubDateArchive` component provides an additional tag-based archive. It works similar to the 
Post list page, as described on the [RainLab.Blog plugin](https://octobercms.com/plugin/rainlab-blog),
but supports one additional parameter: `dateFilter` (which is usually set to `{{ :date }}`).

To support year, month and day we recommend using the following URL syntax on the desired page:

```
url = "/blog/date/:date|^[0-9]{4}(\-[0-9]{2}(\-[0-9]{2}))?/:page?"
```

### bloghubTagArchive

The `bloghubTagArchive` component provides an additional tag-based archive. It works similar to the 
Post list page, as described on the [RainLab.Blog plugin](https://octobercms.com/plugin/rainlab-blog),
but supports one additional parameter: `tagFilter` (which is usually set to `{{ :slug }}`).
