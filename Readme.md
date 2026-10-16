Q1 — PHP / WP_Query
Original code

php
$page = $_GET['page'] ?? 1;
$q = WP_Query([
'posts_per_page' => 10,
'page' => $page,
's' => $\_GET['s']
]);
Errors and their effects

Error 1 — 'page' is the wrong argument.
WP_Query does not use 'page' for pagination. 'page' is used for splitting a single post into multiple pages. The correct argument is 'paged'. Effect: the query does not paginate at all. It always returns the first 10 posts, no matter what page number is passed in.

Error 2 — $\_GET['s'] is unguarded.
If the search parameter is not present in the URL, PHP emits a notice and the 's' argument is set to null. Effect: unnecessary PHP warnings, and the query may behave unpredictably depending on the PHP version.

Error 3 — inputs are not sanitized.
'page' should be cast to an integer. 's' should be passed through sanitize_text_field.

Error 4 — the result is never used.
WP_Query returns a query object, not posts. The code stores it in $q and never loops through it. Effect: nothing is displayed at all. No output, no ad replacement, nothing.

Error 5 — no [ad] replacement.
The requirement is to replace each literal [ad] marker in the post content with an HTML <div> for an ad script. The code never touches post content. Effect: no ads are inserted.

Error 6 — no wp_reset_postdata() after the loop.
WP_Query alters the global $post. If the code loops through the results and then runs another query without resetting, the second query returns incorrect data. Effect: subtle bugs downstream.

Assumptions about the [ad] placeholders

I assume [ad] appears literally in the post content as plain text, not as a shortcode registered with WordPress. If it were a shortcode, WordPress would handle it automatically. I also assume every occurrence should be replaced, not just the first. str_replace handles this by default.

Corrected version

php

<?php
// Sanitize inputs
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Build the query
$args = [
    'post_type'      => 'post',
    'posts_per_page' => 10,
    'paged'          => $page,
    'post_status'    => 'publish',
];

if ($search !== '') {
    $args['s'] = $search;
}

$q = new WP_Query($args);

// Loop and render
if ($q->have_posts()) {
    while ($q->have_posts()) {
        $q->the_post();

        $content = get_the_content();
        $ad_div = '<div class="ad-slot"></div>';
        $content = str_replace('[ad]', $ad_div, $content);

        echo '<article>';
        echo '<h2>' . esc_html(get_the_title()) . '</h2>';
        echo apply_filters('the_content', $content);
        echo '</article>';
    }
} else {
    echo '<p>No results found.</p>';
}

wp_reset_postdata();
Checks I would run

Missing parameters. Load with no ?page= and no ?s=. Expect page 1 of all posts, no PHP notices.

Page 2. Load ?page=2. Expect posts 11 through 20, not the same first 10.

No search results. Load ?s=zzzznonexistentterm. Expect the "No results found" message, no PHP errors.

Multiple [ad] markers. Create a post with three markers. Expect three ad <div>s in the correct positions.

Invalid page number. Load ?page=abc or ?page=-1. Expect page 1, no PHP errors.
