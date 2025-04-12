<?php
/**
 * Displays the "Top Bar Right Widgets". This sidebar can be overridden in child themes by creating a file of the same name.
 *
 * @since 3.8.4
 * @package Suffusion
 * @subpackage Sidebars
 */

if (!suffusion_is_sidebar_empty(7)) {
?>
    <div id="top-bar-right-widgets" class="warea">
        <?php dynamic_sidebar('Top Bar Right Widgets'); ?>
    </div>
<?php
}
?> 