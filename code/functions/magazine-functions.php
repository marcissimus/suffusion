<?php
/**
 * Magazine template function to get headlines
 *
 * @return array
 */
function suffusion_get_headlines() {
	global $post, $wpdb;
	$headlines = array();
	$solos = array();
	$suf_mag_headline_limit = $GLOBALS['suf_mag_headline_limit'] ?? 5;
	$suf_mag_headline_limit = (int)$suf_mag_headline_limit;
	$quota_full = false;

	$querystr = $wpdb->prepare("
		SELECT wposts.*
		FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
		WHERE wposts.ID = wpostmeta.post_id
		AND wpostmeta.meta_key = %s
		AND wpostmeta.meta_value = %s
		AND wposts.post_status = %s
		AND wposts.post_type = %s
		ORDER BY wposts.post_date DESC
	", 'suf_magazine_headline', 'on', 'publish', 'post');

	$head_posts = $wpdb->get_results($querystr, OBJECT);
	foreach ($head_posts as $post) {
		setup_postdata($post);
		$headlines[] = $post;
		$solos[] = $post->ID;
		if (count($headlines) == $suf_mag_headline_limit) {
			$quota_full = true;
			break;
		}
	}

	if ($quota_full) {
		return $headlines;
	}

	$headline_categories = suffusion_get_allowed_categories('suf_mag_headline_categories');
	if (is_array($headline_categories) && count($headline_categories) > 0) {
		$query_cats = array();
		foreach ($headline_categories as $headline_category) {
			$query_cats[] = $headline_category->cat_ID;
		}
		$query_posts = implode(",", array_values($query_cats));
		$cat_query = new WP_Query(array(
			'cat' => $query_posts, 
			'post__not_in' => $solos,
			'posts_per_page' => $suf_mag_headline_limit - count($headlines)
		));
	}

	if (isset($cat_query->posts) && is_array($cat_query->posts)) {
		while ($cat_query->have_posts()) {
			$cat_query->the_post();
			$headlines[] = $post;
			if (count($headlines) == $suf_mag_headline_limit) {
				$quota_full = true;
				break;
			}
		}
	}
	return $headlines;
}

/**
 * Magazine template function to build queries for individual magazine sections.
 *
 * @param array $args
 * @return array
 */
function suffusion_get_mag_section_queries($args = array()) {
	global $post, $wpdb;
	$meta_check_field = $args['meta_check_field'] ?? '';
	$solos = array();
	$queries = array();

	if ($meta_check_field) {
		$querystr = $wpdb->prepare("
			SELECT wposts.*
			FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
			WHERE wposts.ID = wpostmeta.post_id
			AND wpostmeta.meta_key = %s
			AND wpostmeta.meta_value = %s
			AND wposts.post_status = %s
			AND wposts.post_type = %s
			ORDER BY wposts.post_date DESC
		", $meta_check_field, 'on', 'publish', 'post');

		$post_results = $wpdb->get_results($querystr, OBJECT);
		foreach ($post_results as $post) {
			setup_postdata($post);
			$solos[] = $post->ID;
		}
	}

	if (count($solos) > 0) {
		$solo_query = new WP_Query(array('post__in' => $solos, 'ignore_sticky_posts' => 1));
		$queries[] = $solo_query;
	}

	$category_prefix = $args['category_prefix'] ?? '';
	$suf_mag_total_excerpts = $GLOBALS['suf_mag_total_excerpts'] ?? 5;

	if ($category_prefix) {
		$categories = suffusion_get_allowed_categories($category_prefix);
		if (is_array($categories) && count($categories) > 0) {
			$query_cats = array();
			foreach ($categories as $category) {
				$query_cats[] = $category->cat_ID;
			}
			$query_posts = implode(",", array_values($query_cats));
			$cat_query = new WP_Query(array(
				'cat' => $query_posts, 
				'post__not_in' => $solos, 
				'posts_per_page' => (int)$suf_mag_total_excerpts
			));
			$queries[] = $cat_query;
		}
	}
	return $queries;
}
