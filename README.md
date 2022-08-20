BlogHub - OctoberCMS Plugin
===========================

**BlogHub** extends the [RainLab.Blog](https://octobercms.com/plugin/rainlab-blog) OctoberCMS 
extension with some additional data and details. This extension is especially designed for our own 
OctoberCMS templates, but can also be used by any OctoberCMS user and developer, of course.


Features
--------

- Extends the backend user with a 'Display Name' and 'Author Slug'
- Counts (Unique) Views, excluding logged-in backend users
- Implements Custom Meta Data (template-based via `theme.yaml`)
- Implements Global Custom Meta Data (configurable via backend)
- Adds Blog Tags with Promote flag, Description and Color
- Supports Author related archive via component (`bloghubAuthorArchive`)
- Supports Date related archive via component (`bloghubDateArchive`), supports year, month and day
- Supports Tag related archive via component (`bloghubTagArchive`)
- Supports Tags list via component (`bloghubTags`)
- Enhances Post and User models with additional methods and properties. 


Requirements
-------------

- PHP 7.4+ / 8.0+
- OctoberCMS v2 / v3
- Plugin: [RainLab.Blog](https://octobercms.com/plugin/rainlab-blog)


Copyright
---------

Copyright © 2022 rat.md.<br/>
Published under the MIT-License.
