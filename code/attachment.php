<?php
get_header();
?>
<div id="main-col">
	<?php suffusion_before_begin_content(); ?>
	<div id="content">
<?php
$post = $GLOBALS['post'] ?? null;
if (have_posts()) {
	while (have_posts()) {
		the_post();
		$original_post = $post;
?>
	<article <?php post_class();?> id="post-<?php the_ID(); ?>">
<?php suffusion_after_begin_post(); ?>
		<div class="entry-container fix">
			<div class="entry fix">
<?php
	suffusion_attachment();
	suffusion_content();
?>
			</div><!--/entry -->
<?php
		// Due to the inclusion of Ad Hoc Widgets the global variable $post might have got changed. We will reset it to the original value.
		$GLOBALS['post'] = $original_post;
		suffusion_after_content();
?>
		</div><!-- .entry-container -->
<?php
		suffusion_before_end_post();
		$mime = get_post_mime_type();
		$mime = strpos($mime, '/') !== false ? substr($mime, 0, strpos($mime, '/')) : $mime;
		$comments_disabled_var = "suf_{$mime}_comments";
		
		if (isset($GLOBALS[$comments_disabled_var])) {
			$comments_disabled = $GLOBALS[$comments_disabled_var];
			if (!$comments_disabled) {
				comments_template();
			}
		}
		else {
			comments_template();
		}
?>
	</article><!--/post -->
<?php
	}
}
else {
?>
        <article class="post fix">
		<p><?php _e('Sorry, no posts matched your criteria.', 'suffusion'); ?></p>
        </article><!--post -->

<?php
}
?>
	</div><!-- content -->
</div><!-- main col -->
<?php
get_footer();
?>