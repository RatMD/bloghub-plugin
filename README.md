BlogHub - OctoberCMS Plugin
===========================
**BlogHub** enhances the [RainLab.Blog](https://octobercms.com/plugin/rainlab-blog) plugin for 
OctoberCMS by introducing a variety of essential features that streamline blog management and 
improve user experience. From moderatable comments to advanced tagging and custom meta fields, 
BlogHub offers valuable tools for both content creators and developers. It also includes additional 
archives, basic statistics, and more to give you deeper insights into your blog's performance.

Although designed to integrate seamlessly with our custom OctoberCMS templates, **BlogHub** is fully 
compatible with any OctoberCMS setup. Explore the documentation for detailed information on its 
features and usage.

## Features
This is a brief overview of what **BlogHub** offers. For full details, please refer to the docs.

- **Moderatable comments** with features like likes, dislikes, favorites, AJAX support, ...
- **Customizable** global and theme-specific post **meta fields**
- **Promotable** blog **tags** with title, description, and color options
- **View** and **visitor counters** for individual blog posts
- Additional **template components** for creating author, date, and tag archives
- **Extended post model** with numerous extra values and properties
- **Extended user model** also enhanced with additional values and properties
- **Dashboard widgets** for managing comments, posts, and viewing statistics

## Important Notes
1. **Author Archive Pages:** BlogHub allows for the creation of author archive pages. We recommend 
setting the "Author Slug" for each backend user to avoid exposing login names, which are used by 
default. You can also disable the use of login names with the `[bloghubBase]` component as described 
in the documentation.

2. **Blog Comments:** For comment functionality, make sure the `{% framework %}` October-specific 
Twig tag is added to your template layouts if not already present. We're working on an AJAX-less or 
optional solution for a future update.

## Requirements
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

## Copyright
Copyright © 2022 - 2024 rat.md. \
Published under the MIT-License.
