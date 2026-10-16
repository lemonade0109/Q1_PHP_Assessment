# BCM Assessment Q1

## Assumption
Every literal `[ad]` marker represents the same ad slot and becomes an empty `<div class="ad">` for a separate script to populate.

## Fixes
- Use `new WP_Query(...)`.
- Default missing `s` to an empty string; unslash and sanitize query inputs.
- Use `paged` for WordPress listing pagination and `max_num_pages` for the page count.
- Escape displayed values and post titles.
- Match literal brackets with `/\[ad\]/`; return markup from the callback instead of echoing.
- Handle empty results and reset post data.

## Checks to perform in WordPress
1. Missing `page` and `s`: defaults to page 1 and an empty search.
2. `?page=2`: requests the second page.
3. Search with no matches: displays “No posts found”.
4. Content containing `[ad]` twice: produces two ad divs.
5. Confirm the pagination URL format matches the target theme's URL structure.

Verification note: this example has not been run inside a live WordPress installation. Test it in the target theme before submission.
