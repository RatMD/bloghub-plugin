BlogHub - OctoberCMS Plugin
===========================
**BlogHub** extends the [RainLab.Blog](https://octobercms.com/plugin/rainlab-blog) OctoberCMS plugin 
with many necessary and helpful features such as Moderatable Comments, Promotable Tags, Custom Meta 
Fields, additional Archives, basic Statistics, Views counter and more.

This extension is especially designed for our own OctoberCMS templates, but can also be used by any 
OctoberCMS user and developer, of course. Check out the documentation for all details.

## Features
The following list just provides a slight overview, visit the documentation for more details.

- Moderatable **Blog Comments**, with like, dislike, favorite, ajax and more
- **Custom** global and theme-related Post **Meta Fields**
- Promotable **Blog Tags**, with title, description and color
- **View** and **Visitor Counter**, for each single post
- Additional **Template Components**, to create author, date, tag archives and more
- **Extended Post Model**, with many additional values and properties
- **Extended User Model**, also with many additional values and properties
- **Dashboard Widgets**, for your comments, posts and statistics

## Important Notes
1. The RatMD.BlogHub extension adds the possibility to use author archive pages. It is highly 
recommended using the "Author Slug" field for each backend user so that the login name - which would 
be used instead - is not disclosed. You can also disable the use of the login name with the 
`[bloghubBase]` component, as described in the documentation.

2. The Blog comments currently requires to add the `{% framework %}` October-specific TWIG tag to 
your template layouts, when not already included. We're working on an AJAX-less / -optional 
solution for a future update.

## Requirements
- OctoberCMS v3
- PHP 7.4+ / 8.0+
- [Gregwar/Captcha](https://github.com/Gregwar/Captcha)
- [RainLab.Blog](https://octobercms.com/plugin/rainlab-blog)
- **Supports:** [RainLab.User](https://octobercms.com/plugin/rainlab-user) (optional)
- **Supports:** [RainLab.Pages](https://octobercms.com/plugin/rainlab-pages) (optional)
- **Supports:** [RainLab.Translate](https://octobercms.com/plugin/rainlab-translate) (optional)

## Copyright
Copyright © 2022 - 2024 rat.md. \
Published under the MIT-License.
