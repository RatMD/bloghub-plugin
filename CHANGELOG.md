BlogHub - Changelog
===================

Version 1.2.1
-------------
- Add: 'About Me' field for backend users.


Version 1.2.0
-------------
- Add: Extend `backend_users` table with `display_name` and `author_slug` fields.
- Add: Missing URL on the Preview button.
- Add: Date archive type: 'year', 'month' or 'day'.
- Add: Formatted date archive string.
- Add: Dynamic BackendUser method `bloghub_display()` to receive user name.
- Add: Dynamic BackendUser method `bloghub_slug()` to receive author slug or login name.
- Update: Return 404 when no author is found ([bloghubAuthorArchive] Component).
- Update: Return 404 when no date is found or date is invalid ([bloghubDateArchive] Component).
- Update: Return 404 when no tag is found ([bloghubTagArchive] Component).
- Update: Use `author_slug` instead of login (when `author_slug` is not empty).


Version 1.1.0
-------------
- Add: View / Unique View table and counter system. 
- Add: Tags list component (similar to the Categories list component).
- Add: `posts_count` belongsToMany value and getter.
- Add: `Visitor` model.
- Add: `views` and `unique_views` sorting options to the Post model.
- Update: Move Post list by tag slug component from `Tags` to `Tag`.


Version 1.0.0
-------------
- Initial Release