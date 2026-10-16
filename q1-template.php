<?php
/**
 * BCM Assessment Q1.
 * Assumption: every literal [ad] marker is the same empty ad slot,
 * which a separate advertising script will populate.
 */
$current_page = isset($_GET['page']) ? absint(wp_unslash($_GET['page'])) : 1;
$current_page = max(1, $current_page);

$search_term = isset($_GET['s'])
    ? sanitize_text_field(wp_unslash($_GET['s']))
    : '';

$query = new WP_Query([
    'posts_per_page' => 10,
    'paged'          => $current_page,
    's'              => $search_term,
]);
$total_pages = (int) $query->max_num_pages;
?>
<h1>
    Page <?php echo esc_html((string) $current_page); ?>
    of <?php echo esc_html((string) max(1, $total_pages)); ?>
    : Search for <?php echo esc_html($search_term); ?>
</h1>

<?php if ($query->have_posts()) : ?>
    <?php while ($query->have_posts()) : $query->the_post(); ?>
        <?php
        $content = apply_filters('the_content', get_the_content());
        $content = preg_replace_callback(
            '/\[ad\]/',
            static function ($matches) {
                return '<div class="ad" aria-label="Advertisement"></div>';
            },
            $content
        );
        ?>
        <article>
            <h2><?php echo esc_html(get_the_title()); ?></h2>
            <div class="post-content"><?php echo $content; // Filtered WordPress content contains intended HTML. ?></div>
        </article>
    <?php endwhile; ?>

    <?php if ($total_pages > 1) : ?>
        <nav aria-label="Posts pagination">
            <?php
            echo wp_kses_post(paginate_links([
                'total'    => $total_pages,
                'current'  => $current_page,
                'format'   => '?page=%#%',
                'add_args' => $search_term !== '' ? ['s' => $search_term] : false,
                'type'     => 'list',
            ]));
            ?>
        </nav>
    <?php endif; ?>
<?php else : ?>
    <p>No posts found<?php echo $search_term !== '' ? ' for “' . esc_html($search_term) . '”' : ''; ?>.</p>
<?php endif; ?>

<?php wp_reset_postdata(); ?>
