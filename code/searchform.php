<?php
/**
 * Search form
 *
 * @package Suffusion
 * @subpackage Templates
 */

global $suffusion_rhw_is_not_dynamic, $suf_color_scheme;
$collapse = '';

if (isset($suffusion_rhw_is_not_dynamic) && 
    $suffusion_rhw_is_not_dynamic === true && 
    in_array($suf_color_scheme, ['photonique', 'scribbles'], true)) {
    $collapse = 'collapse';
}
?>

<form method="get" class="searchform <?php echo esc_attr($collapse); ?>" action="<?php echo esc_url(home_url('/')); ?>/">
	<input type="text" name="s" class="searchfield"
		<?php if ($collapse === '') { ?>
			placeholder="<?php echo esc_attr__('Search', 'suffusion'); ?>"
		<?php } ?>
		/>
	<input type="submit" class="searchsubmit" value="" name="searchsubmit" />
	<?php if (function_exists('icl_object_id')) { ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr(ICL_LANGUAGE_CODE); ?>"/>
	<?php } ?>
</form>
