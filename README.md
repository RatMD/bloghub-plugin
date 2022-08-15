BlogHub - OctoberCMS Plugin
===========================

**BlogHub** extends the [RainLab.Blog](https://octobercms.com/plugin/rainlab-blog) OctoberCMS 
extension with some additional data and details. This extension is especially designed for our own 
OctoberCMS templates, but can also be used by any OctoberCMS user and developer, of course.

This extension provides, next to the custom meta data and blog tags, also additional blog archives 
for the author, date and provided tags. **BlogHub** also extends the `Post` Model with some 
additional dynamic methods, for example to receive related or random posts.

Features
--------

- Custom Meta Data (template-based via `theme.yaml`)
- Global Custom Meta Data (configurable via backend)
- Blog Tags with Promote flag, Description and Color
- Author related archive component (`bloghubAuthorArchive`)
- Date related archive component (`bloghubTagArchive`), supports year, month and day
- Tag related archive component (`bloghubTagArchive`)
- Additional Dynamic Post model methods (see documentation)


Requirements
-------------

- PHP 7.4+ / 8.0+
- OctoberCMS v2 / v3
- Plugin: [RainLab.Blog](https://octobercms.com/plugin/rainlab-blog)


Copyright
---------

Copyright © 2022 rat.md.<br/>
Published under the MIT-License.
