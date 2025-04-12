<?php
/**
 * Template Name: Custom Post Type Archive
 *
 * Shows all posts of a specific post type. You can configure how you want your display, e.g. full posts, tiles, etc.
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
		$post_id = -1;
		if (have_posts()) {
			while (have_posts()) {
				the_post();
				$post = get_post();
				$post_id = $post->ID;
			}
		} else {
			get_template_part('layouts/template-missing');
		}

		if (isset($post_id) && $post_id > 0) {
			global $wp_query, $suffusion_cpt_post_id;
			$original_query = $wp_query;
			$suffusion_cpt_post_id = $post_id;
			
			$args = [
				'post_type' => 'post',
				'posts_per_page' => get_option('posts_per_page'),
				'order_by' => 'date',
				'order' => 'DESC',
				'paged' => get_query_var('paged') ?: 1
			];

			$post_type = suffusion_get_post_meta($post_id, 'suf_cpt_post_type', true);
			if ($post_type) {
				$args['post_type'] = $post_type;
			}

			$total_posts = suffusion_get_post_meta($post_id, 'suf_cpt_total_posts', true);
			$args['posts_per_page'] = $total_posts ?: $args['posts_per_page'];

			$order_by = suffusion_get_post_meta($post_id, 'suf_cpt_order_by', true);
			if ($order_by) {
				$args['order_by'] = $order_by;
			}

			$order = suffusion_get_post_meta($post_id, 'suf_cpt_order', true);
			if ($order) {
				$args['order'] = $order;
			}

			$post_type_layout = suffusion_get_post_meta($post_id, 'suf_cpt_post_type_layout', true) ?: 'full-post';

			$full_posts = suffusion_get_post_meta($post_id, 'suf_cpt_full_posts', true);
			if ($full_posts > $total_posts) {
				$full_posts = $total_posts;
			}

			global $suf_cpt_bylines_post_date, $suf_cpt_bylines_posted_by, 
				   $suf_cpt_bylines_comments, $suf_cpt_bylines_permalinks, 
				   $suf_cpt_show_tile_byline, $suf_cpt_byline_type;
			
			$suf_cpt_byline_type = suffusion_get_post_meta($post_id, 'suf_cpt_byline_type', true);
			$suf_cpt_bylines_post_date = suffusion_get_post_meta($post_id, 'suf_cpt_bylines_post_date', true);
			$suf_cpt_bylines_posted_by = suffusion_get_post_meta($post_id, 'suf_cpt_bylines_posted_by', true);
			$suf_cpt_bylines_comments = suffusion_get_post_meta($post_id, 'suf_cpt_bylines_comments', true);
			$suf_cpt_bylines_permalinks = suffusion_get_post_meta($post_id, 'suf_cpt_bylines_permalinks', true);
			$byline_taxonomies = suffusion_get_post_meta($post_id, 'suf_cpt_byline_taxonomies', true);
			$suf_cpt_show_tile_byline = suffusion_get_post_meta($post_id, 'suf_cpt_show_tile_byline', true);

			add_filter('suffusion_byline_position', 'suffusion_cpt_byline_position');

			$args = apply_filters('suffusion_cpt_archive_query', $args);
			$wp_query = new WP_Query($args);
			
			match($post_type_layout) {
				'list' => get_template_part('layouts/layout-list'),
				'tiles' => (function() {
					suffusion_after_begin_content();
					get_template_part('layouts/layout-tiles');
				})(),
				'mosaic' => get_template_part('layouts/layout-mosaic'),
				default => (function() {
					suffusion_after_begin_content();
					get_template_part('layouts/layout-blog');
				})(),
			};
			
			$wp_query = $original_query;
		}
		?>
	</div><!-- content -->
</div><!-- main col -->
<?php get_footer(); ?>