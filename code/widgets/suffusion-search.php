<?php
/**
 * Defines a Search widget that overrides the default WP Search Widget.
 *
 * @package Suffusion
 * @subpackage Widgets
 *
 */
class Suffusion_Search extends WP_Widget {
	function __construct() {
		$widget_ops = array(
			"classname" => "search",
			"description" => __("A search form for your blog", "suffusion"),
		);
		$control_ops = array(
			"id_base" => "search"
		);
		parent::__construct("search", __("Search", "suffusion"), $widget_ops, $control_ops);
	}

	function widget($args, $instance) {
		extract($args);

		$title = apply_filters("widget_title", $instance["title"]);
		echo $before_widget;
		if (trim($title) != "") {
			echo $before_title.$title.$after_title;
		}
		get_search_form();
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance["title"] = strip_tags($new_instance["title"]);

		return $instance;
	}

	function form($instance) {
		$defaults = array("title" => __("Search", "suffusion"));
		// remove null and empty string values
		$instance = array_filter($instance, fn($value) => (!is_null($value) && $value !== ''));
		$instance = wp_parse_args((array)$instance, $defaults);
?>

	<!-- Widget Title: Text Input -->
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'suffusion'); ?></label>
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
	</p>

	<?php
	}
}
?>
