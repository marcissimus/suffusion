<?php
/**
 * Displays the Header Widgets. These appear at the same level as the header image.
 *
 * @since 3.8.4
 * @package Suffusion
 * @subpackage Sidebars
 */

if (!suffusion_is_sidebar_empty(12)) {
?>
	<div id="header-widgets" class="warea">
		<?php dynamic_sidebar('Header Widgets'); ?>
	</div>
<?php
}
