<?php
/**
 * Template Name: Sitemap
 *
 * Displays an HTML-based sitemap for your site.
 *
 * @package Suffusion
 * @subpackage Template
 */
get_header();
?>
<div id="main-col">
<?php suffusion_before_begin_content(); ?>
	<div id="content" class='hfeed content'>
<?php
if (have_posts()) {
	while (have_posts()) {
		the_post();
		$post = get_post();
		$original_post = $post;
		do_action('suffusion_before_post', $post->ID, 'blog', 1);
?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php suffusion_after_begin_post(); ?>
			<div class="entry-container fix">
				<div class="entry fix">
					<?php
						suffusion_content();
						global $suf_sitemap_contents, $suf_sitemap_entity_order, $suffusion_sitemap_entities;
						$show = explode(',', $suf_sitemap_contents);
						
						if (!empty($show)) {
							$entity_order = is_array($suf_sitemap_entity_order) 
								? array_keys($suf_sitemap_entity_order)
								: explode(',', $suf_sitemap_entity_order);

							foreach ($entity_order as $entity) {
								if (in_array($entity, $show, true)) {
									$title_opt = 'suf_sitemap_label' . $suffusion_sitemap_entities[$entity]['opt'];
									$title = $$title_opt;
					?>
						<h3><?php echo esc_html($title); ?></h3>
						<ul class='xoxo <?php echo esc_attr($entity); ?>'>
								<?php
									match($entity) {
										'pages' => wp_list_pages(['title_li' => false]),
										'categories' => wp_list_categories(['show_count' => true, 'use_desc_for_title' => false, 'title_li' => false]),
										'authors' => wp_list_authors(['exclude_admin' => false, 'optioncount' => true, 'title_li' => false]),
										'years' => wp_get_archives(['type' => 'yearly', 'show_post_count' => true]),
										'months' => wp_get_archives(['type' => 'monthly', 'show_post_count' => true]),
										'weeks' => wp_get_archives(['type' => 'weekly', 'show_post_count' => true]),
										'days' => wp_get_archives(['type' => 'daily', 'show_post_count' => true]),
										'tag-cloud' => wp_tag_cloud(['number' => 0]),
										'posts' => wp_get_archives(['type' => 'postbypost']),
										default => null,
									};
								?>
						</ul><!-- /<?php echo esc_attr($entity); ?> -->
					<?php
								}
							}
						}
					?>
				</div><!-- .entry -->
			</div><!-- .entry-container -->
			<?php
			$post = $original_post;
			suffusion_before_end_post();
			comments_template();
			?>
		</article><!-- .post -->
<?php
		do_action('suffusion_after_post', $post->ID, 'blog', 1);
	}
}
?>
	</div><!-- content -->
</div><!-- main col -->
<?php get_footer(); ?>
