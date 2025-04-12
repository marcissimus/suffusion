<?php
/**
 * Renders the theme options. This file is to be loaded only for the admin screen
 */

global $suffusion_options_file, $suffusion_options_intro_page, $suffusion_options_theme_skinning_page, $suffusion_options_visual_effects_page, $suffusion_options_sidebars_and_widgets_page, $suffusion_options_blog_features_page, $suffusion_options_templates_page;

class Suffusion_Options_Renderer {
	private array $options;
	private array $option_structure;
	private string $file;
	private array $hidden_options;
	private array $shown_options;
	private array $nested_options;
	private array $reverse_options;
	private array $option_defaults;
	private array $allowed_values;
	private int $displayed_sections;
	private ?string $previous_displayed_section;
	
	public function __construct(array $options, string $file) {
		$this->options = $options;
		$this->file = $file;
		$this->displayed_sections = 0;
		$this->shown_options = [];
		$this->reverse_options = [];
		$this->option_defaults = [];
		$this->allowed_values = [];
		$all_options = get_option('suffusion_options');
		$this->hidden_options = !isset($all_options) ? [] : $all_options;

		foreach ($options as $option) {
			if (isset($option['id'])) {
				$this->shown_options[] = $option['id'];
				$this->reverse_options[$option['id']] = $option['type'];
				if (isset($option['std'])) {
					$this->option_defaults[$option['id']] = $option['std'];
				}
				if (isset($option['options'])) {
					$this->allowed_values[$option['id']] = $option['options'];
				}
				if (isset($this->hidden_options[$option['id']])) {
					unset($this->hidden_options[$option['id']]);
				}
			}
		}
	}

	/**
	 * Renders an option whose type is "title". Invoked by add_settings_field.
	 */
	public function create_title(array $value): void {
		echo '<h2 class="suf-header-1">' . esc_html($value['name']) . "</h2>\n";
	}

	/**
	 * Renders an option whose type is "suf-header-2". Invoked by add_settings_field.
	 */
	public function create_suf_header_2(array $value): void {
		echo '<h3 class="suf-header-2">' . esc_html($value['name']) . "</h3>\n";
	}

	/**
	 * Renders an option whose type is "suf-header-3". Invoked by add_settings_field.
	 */
	public function create_suf_header_3(array $value): void {
		echo '<h3 class="suf-header-3">' . esc_html($value['name']) . "</h3>\n";
	}

	/**
	 * Creates the opening markup for each option.
	 */
	private function create_opening_tag(array $value): void {
		$group_class = isset($value['grouping']) ? "suf-grouping-rhs" : "";
		
		echo '<div class="suf-section fix">' . "\n";
		if ($group_class !== "") {
			echo "<div class='$group_class fix'>\n";
		}
		if (isset($value['name'])) {
			echo "<h3>" . esc_html($value['name']) . "</h3>\n";
		}
		if (isset($value['desc']) && !(isset($value['type']) && $value['type'] === 'checkbox')) {
			echo wp_kses_post($value['desc']) . "<br />";
		}
		if (isset($value['note'])) {
			echo "<span class=\"note\">" . wp_kses_post($value['note']) . "</span><br />";
		}
	}

	/**
	 * Creates the closing markup for each option.
	 */
	private function create_closing_tag(array $value): void {
		if (isset($value['grouping'])) {
			echo "</div>\n";
		}
		echo "</div><!-- suf-section -->\n";
	}

	/**
	 * Creates an option-grouping within a section. Invoked by add_settings_field.
	 */
	public function create_suf_grouping(array $value): void {
		echo "<div class='" . esc_attr($value['category']) . "-grouping suf-section grouping fix'>\n";
		echo "<h3 class='suf-group-handler'>" . esc_html($value['name']) . "</h3>\n";
		if (isset($value['desc'])) {
			echo wp_kses_post($value['desc']) . "<br />";
		}
		echo "</div>\n";
	}

	/**
	 * Renders an option whose type is "text". Invoked by add_settings_field.
	 */
	public function create_section_for_text(array $value): void {
		global $suffusion_options;
		$this->create_opening_tag($value);
		
		$text = $suffusion_options[$value['id']] ?? $value['std'];
		$text = stripslashes($text);
		$text = esc_attr($text);

		echo '<input type="text" name="suffusion_options[' . esc_attr($value['id']) . ']" value="' . $text . '" />' . "\n";
		if (isset($value['hint'])) {
			echo " &laquo; " . esc_html($value['hint']) . "<br />\n";
		}
		$this->create_closing_tag($value);
	}

	/**
	 * Renders an option whose type is "textarea". Invoked by add_settings_field.
	 */
	public function create_section_for_textarea(array $value): void {
		global $suffusion_options;
		$this->create_opening_tag($value);
		
		$text = '';
		if (isset($suffusion_options[$value['id']]) && $suffusion_options[$value['id']] !== "") {
			$text = stripslashes($suffusion_options[$value['id']]);
			$text = esc_attr($text);
		} else {
			$text = $value['std'];
		}
		
		echo '<textarea name="suffusion_options[' . esc_attr($value['id']) . ']" cols="" rows="">' . "\n";
		echo $text;
		echo '</textarea>';
		
		if (isset($value['hint'])) {
			echo " &laquo; " . esc_html($value['hint']) . "<br />\n";
		}
		$this->create_closing_tag($value);
	}

	/**
	 * Renders an option whose type is "select". Invoked by add_settings_field.
	 */
	public function create_section_for_select(array $value): void {
		global $suffusion_options;
		$this->create_opening_tag($value);
		
		$option_list = apply_filters('suffusion_admin_modify_option_list', $value['options'], $value['id']);
		echo '<select name="suffusion_options[' . esc_attr($value['id']) . ']">' . "\n";
		
		foreach ($option_list as $option_value => $option_text) {
			$option_value = stripslashes($option_value);
			$selected = '';
			if (isset($suffusion_options[$value['id']])) {
				$selected = selected(stripslashes($suffusion_options[$value['id']]), $option_value, false);
			} else {
				$selected = selected($value['std'], $option_value, false);
			}
			
			if ($option_value === $value['std']) {
				$option_text .= ' (Default)';
			}
			
			echo "<option $selected value=\"" . esc_attr($option_value) . "\">" . esc_html($option_text) . "</option>\n";
		}
		echo "</select>\n";
		$this->create_closing_tag($value);
	}

	/**
	 * Renders an option whose type is "multi-select". Invoked by add_settings_field.
	 */
	public function create_section_for_multi_select(array $value): void {
		global $suffusion_options;
		$this->create_opening_tag($value);
		
		echo '<div class="suf-checklist">' . "\n";
		echo '<ul class="suf-checklist" id="' . esc_attr($value['id']) . '-chk" >' . "\n";
		
		$consolidated_value = $value['std'] ?? '';
		if (isset($suffusion_options[$value['id']])) {
			$consolidated_value = $suffusion_options[$value['id']];
		}
		
		$consolidated_value = trim($consolidated_value);
		$exploded = $consolidated_value !== '' ? explode(',', $consolidated_value) : [];

		foreach ($value['options'] as $option_value => $option_list) {
			$checked = " ";
			if ($consolidated_value) {
				foreach ($exploded as $checked_value) {
					$checked = checked($checked_value, $option_value, false);
					if (trim($checked) !== '') {
						break;
					}
				}
			}
			
			$depth = $option_list['depth'] ?? 0;
			echo "<li>\n";
			echo '<label><input type="checkbox" name="' . esc_attr($value['id'] . "_" . $option_value) . 
				 '" value="true" ' . $checked . ' class="depth-' . ($depth + 1) . 
				 ' suf-options-checkbox-' . esc_attr($value['id']) . '" />' . 
				 esc_html($option_list['title']) . "</label>\n";
			echo "</li>\n";
		}
		
		echo "</ul>\n";
		echo "<div class='suf-multi-select-button-panel'>\n";
		echo "<input type='button' name='" . esc_attr($value['id']) . "-button-all' value='Select All' class='button-all suf-multi-select-button' />\n";
		echo "<input type='button' name='" . esc_attr($value['id']) . "-button-none' value='Select None' class='button-none suf-multi-select-button' />\n";
		echo "</div>\n";
		
		$set_value = $suffusion_options[$value['id']] ?? $value['std'] ?? "";
		echo '<input type="hidden" name="suffusion_options[' . esc_attr($value['id']) . ']" id="' . 
			 esc_attr($value['id']) . '" value="' . esc_attr($set_value) . '"/>' . "\n";
		echo "</div>\n";
		
		$this->create_closing_tag($value);
	}

	/**
	 * Renders an option whose type is "radio". Invoked by add_settings_field.
	 */
	public function create_section_for_radio(array $value): void {
		global $suffusion_options;
		$this->create_opening_tag($value);
		
		$option_list = apply_filters('suffusion_admin_modify_option_list', $value['options'], $value['id']);
		foreach ($option_list as $option_value => $option_text) {
			$option_value = stripslashes($option_value);
			$checked = '';
			
			if (isset($suffusion_options[$value['id']])) {
				$checked = checked(stripslashes($suffusion_options[$value['id']]), $option_value, false);
			} else {
				$checked = checked($value['std'], $option_value, false);
			}
			
			$option_class = $option_value === $value['std'] ? 'default-value' : '';
			
			echo '<div class="suf-radio ' . esc_attr($option_class) . '"><label>' .
				 '<input type="radio" name="suffusion_options[' . esc_attr($value['id']) . ']" ' .
				 'value="' . esc_attr($option_value) . '" ' . $checked . "/>" . 
				 esc_html($option_text) . "</label></div>\n";
		}
		$this->create_closing_tag($value);
	}

	/**
	 * Renders an option whose type is "checkbox". Invoked by add_settings_field.
	 */
	public function create_section_for_checkbox(array $value): void {
		global $suffusion_options;
		
		$checked = '';
		if (isset($suffusion_options[$value['id']])) {
			$checked = checked(stripslashes($suffusion_options[$value['id']]), 'on', false);
		}
		
		$this->create_opening_tag($value);
		echo '<label><input type="checkbox" name="suffusion_options[' . esc_attr($value['id']) . ']" ' . 
			 $checked . "/>" . esc_html($value['desc']) . "</label>\n";
		$this->create_closing_tag($value);
	}

	/**
	 * Renders an option whose type is "color-picker". Invoked by add_settings_field.
	 */
	public function create_section_for_color_picker(array $value): void {
		global $suffusion_options;
		$this->create_opening_tag($value);
		
		$color_value = $suffusion_options[$value['id']] ?? $value['std'];
		if (substr($color_value, 0, 1) !== '#') {
			$color_value = "#$color_value";
		}

		echo '<div class="color-picker">' . "\n";
		echo '<input type="text" id="' . esc_attr($value['id']) . '" name="suffusion_options[' . 
			 esc_attr($value['id']) . ']" value="' . esc_attr($color_value) . 
			 '" class="color color-' . esc_attr($value['id']) . '" /> <br/>' . "\n";
		echo "<strong>Default: " . esc_html($value['std']) . "</strong> (You can copy and paste this into the box above)\n";
		echo "</div>\n";
		
		$this->create_closing_tag($value);
	}

	/**
	 * Renders an option whose type is "upload". Invoked by add_settings_field.
	 */
	public function create_section_for_upload(array $value): void {
		global $suffusion_options;
		$this->create_opening_tag($value);
		
		$upload = $suffusion_options[$value['id']] ?? $value['std'];
		$upload = stripslashes($upload);
		$upload = esc_attr($upload);
		
		$hint = $value['hint'] ?? null;
		$this->display_upload_field($upload, $value['id'], "suffusion_options[{$value['id']}]", $hint);
		$this->create_closing_tag($value);
	}

	/**
	 * Displays an upload field and button. This has been separated from the create_section_for_upload method,
	 * because this is used by the create_section_for_background as well.
	 */
	private function display_upload_field(string $upload, string $id, string $name, ?string $hint = null): void {
		echo '<input type="text" name="' . esc_attr($name) . '" id="' . esc_attr($id) . '" value="' . esc_attr($upload) . '" />' . "\n";
		if ($hint !== null) {
			echo " &laquo; " . esc_html($hint) . "<br />\n";
		}

		echo '<div class="upload-buttons">';
		$hide = empty($upload) ? '' : 'hidden';
		echo '<span class="button image_upload_button ' . $hide . '" id="upload_' . esc_attr($id) . '">Upload Image</span>';

		$hide = !empty($upload) ? '' : 'hidden';
		echo '<span class="button image_reset_button ' . $hide . '" id="reset_' . esc_attr($id) . '">Reset</span>';
		echo '</div>' . "\n";

		if (!empty($upload)) {
			echo "<div id='suffusion-preview-" . esc_attr($id) . "'>\n";
			echo "<p><strong>Preview:</strong></p>\n";
			echo '<img class="suffusion-option-image" id="image_' . esc_attr($id) . '" src="' . esc_url($upload) . '" alt="" />';
			echo "</div>";
		}
	}

	/**
	 * Renders an option whose type is "sortable-list". Invoked by add_settings_field.
	 */
	public function create_section_for_sortable_list(array $value): void {
		global $suffusion_options;
		$this->create_opening_tag($value);
		
		$list_order = $suffusion_options[$value['id']] ?? $value['std'];
		
		if (is_array($list_order)) {
			$list_order_array = $list_order;
			$list_order = implode(',', array_keys($list_order_array));
		} else {
			$defaults = $value['std'];
			$keys = explode(',', $list_order);
			$clean_keys = [];
			$list_order_array = [];
			
			foreach ($keys as $key) {
				if (isset($defaults[$key])) {
					$clean_keys[] = $key;
					$list_order_array[$key] = $defaults[$key];
				}
			}

			foreach ($defaults as $key => $key_value) {
				if (!in_array($key, $clean_keys, true)) {
					$clean_keys[] = $key;
					$list_order_array[$key] = $key_value;
				}
			}
			$list_order = implode(',', $clean_keys);
		}
		?>
		<script type="text/javascript">
		$j = jQuery.noConflict();
		$j(document).ready(function() {
			$j("#<?php echo esc_attr($value['id']); ?>-ui").sortable({
				update: function() {
					$j('input#<?php echo esc_attr($value['id']); ?>').val($j("#<?php echo esc_attr($value['id']); ?>-ui").sortable('toArray'));
				}
			});
			$j("#<?php echo esc_attr($value['id']); ?>-ui").disableSelection();
		});
		</script>
		<?php
		echo "<ul id='" . esc_attr($value['id']) . "-ui' name='" . esc_attr($value['id']) . "-ui' class='suf-sort-list'>\n";
		
		foreach ($list_order_array as $key => $key_value) {
			echo "<li id='" . esc_attr($key) . "' class='suf-sort-list-item'>" . esc_html($key_value) . "</li>";
		}
		
		echo "</ul>\n";
		echo "<input id='" . esc_attr($value['id']) . "' name='suffusion_options[" . esc_attr($value['id']) . "]' type='hidden' value='" . esc_attr($list_order) . "'/>";
		$this->create_closing_tag($value);
	}

	/**
	 * Renders an option whose type is "slider". Invoked by add_settings_field.
	 */
	public function create_section_for_slider(array $value): void {
		global $suffusion_options;
		$this->create_opening_tag($value);
		
		$options = $value['options'];
		$default = $suffusion_options[$value['id']] ?? $value['std'];
		?>
		<script type="text/javascript">
		$j = jQuery.noConflict();
		$j(document).ready(function() {
			$j("#<?php echo esc_attr($value['id']); ?>-slider").slider({
				range: "<?php echo esc_attr($options['range']); ?>",
				value: <?php echo (int)$default; ?>,
				min: <?php echo (int)$options['min']; ?>,
				max: <?php echo (int)$options['max']; ?>,
				step: <?php echo (int)$options['step']; ?>,
				slide: function(event, ui) {
					$j("input#<?php echo esc_attr($value['id']); ?>").val(ui.value);
				}
			});
		});
		</script>

		<div class='slider'>
			<p>
				<input type="text" id="<?php echo esc_attr($value['id']); ?>" 
					   name="suffusion_options[<?php echo esc_attr($value['id']); ?>]" 
					   value="<?php echo esc_attr($default); ?>" 
					   class='slidertext' /> <?php echo esc_html($options['unit']); ?>
			</p>
			<div id="<?php echo esc_attr($value['id']); ?>-slider" style="width:<?php echo esc_attr($options['size']); ?>;"></div>
		</div>
		<?php
		$this->create_closing_tag($value);
	}

	/**
	 * Renders an option whose type is "background". Invoked by add_settings_field.
	 */
	public function create_section_for_background(array $value): void {
		global $suffusion_options;
		$this->create_opening_tag($value);
		
		$original = $value['std'];
		$default_txt = '';
		$default = [];
		
		if (!isset($suffusion_options[$value['id']])) {
			$default = $value['std'];
			$default_txt = implode(';', array_map(
				fn($opt, $opt_val) => "$opt=$opt_val",
				array_keys($value['std']),
				$value['std']
			));
		} else {
			$default_txt = $suffusion_options[$value['id']];
			if (is_array($default_txt)) {
				$default = $default_txt;
				$default_txt = implode(';', array_map(
					fn($opt, $opt_val) => "$opt=$opt_val",
					array_keys($default),
					$default
				));
			}
			$default = $default_txt;
			$vals = explode(';', $default);
			$default = [];
			foreach ($vals as $val) {
				$pair = explode('=', $val);
				if (isset($pair[0], $pair[1])) {
					$default[$pair[0]] = $pair[1];
				} elseif (isset($pair[0])) {
					$default[$pair[0]] = '';
				}
			}
		}

		$repeats = [
			'repeat' => 'Repeat horizontally and vertically',
			'repeat-x' => 'Repeat horizontally only',
			'repeat-y' => 'Repeat vertically only',
			'no-repeat' => 'Do not repeat'
		];

		$positions = [
			'top left' => 'Top left',
			'top center' => 'Top center',
			'top right' => 'Top right',
			'center left' => 'Center left',
			'center center' => 'Middle of the page',
			'center right' => 'Center right',
			'bottom left' => 'Bottom left',
			'bottom center' => 'Bottom center',
			'bottom right' => 'Bottom right'
		];

		foreach ($value['options'] as $option_value => $option_text) {
			$checked = isset($suffusion_options[$value['id']]) 
				? checked($suffusion_options[$value['id']], $option_value, false)
				: checked($value['std'], $option_value, false);
				
			echo '<div class="suf-radio"><input type="radio" name="' . esc_attr($value['id']) . 
				 '" value="' . esc_attr($option_value) . '" ' . $checked . '/>' . 
				 esc_html($option_text) . "</div>\n";
		}
		?>
		<div class='suf-background-options'>
		<table class='opt-sub-table'>
			<col class='opt-sub-table-cols'/>
			<col class='opt-sub-table-cols'/>
			<tr>
				<td valign='top'>
					<div class="color-picker-group">
						<strong>Background Color:</strong><br />
						<input type="radio" name="<?php echo esc_attr($value['id']); ?>-colortype" 
							   value="transparent" <?php checked($default['colortype'], 'transparent'); ?> /> Transparent / No color<br/>
						<input type="radio" name="<?php echo esc_attr($value['id']); ?>-colortype" 
							   value="custom" <?php checked($default['colortype'], 'custom'); ?>/> Custom
						<input type="text" id="<?php echo esc_attr($value['id']); ?>-bgcolor" 
							   name="<?php echo esc_attr($value['id']); ?>-bgcolor" 
							   value="<?php echo esc_attr($default['color']); ?>" class="color" /><br />
						Default: <span><?php echo esc_html($original['color']); ?></span>
					</div>
				</td>
				<td valign='top'>
					<strong>Image URL:</strong><br />
					<?php $this->display_upload_field($default['image'], $value['id'] . '-bgimg', $value['id'] . '-bgimg'); ?>
				</td>
			</tr>

			<tr>
				<td valign='top'>
					<strong>Image Position:</strong><br />
					<select name="<?php echo esc_attr($value['id']); ?>-position" 
							id="<?php echo esc_attr($value['id']); ?>-position">
					<?php
					foreach ($positions as $option_value => $option_text) {
						echo '<option value="' . esc_attr($option_value) . '" ' . 
							 selected($default['position'], $option_value, false) . '>' . 
							 esc_html($option_text) . "</option>\n";
					}
					?>
					</select>
				</td>

				<td valign='top'>
					<strong>Image Repeat:</strong><br />
					<select name="<?php echo esc_attr($value['id']); ?>-repeat" 
							id="<?php echo esc_attr($value['id']); ?>-repeat">
					<?php
					foreach ($repeats as $option_value => $option_text) {
						echo '<option value="' . esc_attr($option_value) . '" ' . 
							 selected($default['repeat'], $option_value, false) . '>' . 
							 esc_html($option_text) . "</option>\n";
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td valign='top' colspan='2'>
					<script type="text/javascript">
					$j = jQuery.noConflict();
					$j(document).ready(function() {
						$j("#<?php echo esc_js($value['id']); ?>-transslider").slider({
							range: "min",
							value: <?php echo (int)$default['trans']; ?>,
							min: 0,
							max: 100,
							step: 1,
							slide: function(event, ui) {
								$j("input#<?php echo esc_js($value['id']); ?>-trans").val(ui.value);
								$j("#<?php echo esc_js($value['id']); ?>").val(
									'color=' + $j("#<?php echo esc_js($value['id']); ?>-bgcolor").val() + ';' +
									'colortype=' + $j("input[name=<?php echo esc_js($value['id']); ?>-colortype]:checked").val() + ';' +
									'image=' + $j("#<?php echo esc_js($value['id']); ?>-bgimg").val() + ';' +
									'position=' + $j("#<?php echo esc_js($value['id']); ?>-position").val() + ';' +
									'repeat=' + $j("#<?php echo esc_js($value['id']); ?>-repeat").val() + ';' +
									'trans=' + $j("#<?php echo esc_js($value['id']); ?>-trans").val() + ';'
								);
							}
						});
					});
					</script>

					<div class='slider'>
						<p>
							<strong>Layer Transparency (not for IE):</strong>
							<input type="text" id="<?php echo esc_attr($value['id']); ?>-trans" 
								   name="<?php echo esc_attr($value['id']); ?>-trans" 
								   value="<?php echo esc_attr($default['trans']); ?>" class='slidertext' />
						</p>
						<div id="<?php echo esc_attr($value['id']); ?>-transslider" class='transslider'></div>
					</div>
				</td>
			</tr>
		</table>
		<input type='hidden' id="<?php echo esc_attr($value['id']); ?>" 
			   name="suffusion_options[<?php echo esc_attr($value['id']); ?>]" 
			   value="<?php echo esc_attr($default_txt); ?>" />
		</div>
		<?php
		$this->create_closing_tag($value);
	}

	/**
	 * Renders an option whose type is "border". Invoked by add_settings_field.
	 */
	public function create_section_for_border(array $value): void {
		global $suffusion_options;
		$this->create_opening_tag($value);
		
		$original = $value['std'];
		if (!isset($suffusion_options[$value['id']])) {
			$default = $value['std'];
			$default_txt = implode('||', array_map(
				function($edge, $edge_val) {
					return $edge . '::' . implode(';', array_map(
						fn($opt, $opt_val) => "$opt=$opt_val",
						array_keys($edge_val),
						$edge_val
					));
				},
				array_keys($value['std']),
				$value['std']
			));
		} else {
			$default_txt = $suffusion_options[$value['id']];
			if (is_array($default_txt)) {
				$default = $default_txt;
				$default_txt = implode('||', array_map(
					function($edge, $edge_val) {
						return $edge . '::' . implode(';', array_map(
							fn($opt, $opt_val) => "$opt=$opt_val",
							array_keys($edge_val),
							$edge_val
						));
					},
					array_keys($default),
					$default
				));
			}
			$default = $default_txt;
			$edge_array = explode('||', $default);
			$default = [];
			
			foreach ($edge_array as $edge_vals) {
				if (trim($edge_vals) !== '') {
					$edge_val_array = explode('::', $edge_vals);
					if (is_array($edge_val_array) && count($edge_val_array) > 1) {
						$vals = explode(';', $edge_val_array[1]);
						$default[$edge_val_array[0]] = [];
						foreach ($vals as $val) {
							$pair = explode('=', $val);
							if (isset($pair[0], $pair[1])) {
								$default[$edge_val_array[0]][$pair[0]] = $pair[1];
							} elseif (isset($pair[0])) {
								$default[$edge_val_array[0]][$pair[0]] = '';
							}
						}
					}
				}
			}
		}

		$edges = [
			'top' => 'Top',
			'right' => 'Right',
			'bottom' => 'Bottom',
			'left' => 'Left'
		];

		$styles = [
			'none' => 'No border',
			'hidden' => 'Hidden',
			'dotted' => 'Dotted',
			'dashed' => 'Dashed',
			'solid' => 'Solid',
			'double' => 'Double',
			'grove' => 'Groove',
			'ridge' => 'Ridge',
			'inset' => 'Inset',
			'outset' => 'Outset'
		];

		$border_width_units = [
			'px' => 'Pixels (px)',
			'em' => 'Em'
		];

		foreach ($value['options'] as $option_value => $option_text) {
			$checked = isset($suffusion_options[$value['id']]) 
				? checked($suffusion_options[$value['id']], $option_value, false)
				: checked($value['std'], $option_value, false);
				
			echo '<div class="suf-radio"><input type="radio" name="' . esc_attr($value['id']) . 
				 '" value="' . esc_attr($option_value) . '" ' . $checked . '/>' . 
				 esc_html($option_text) . "</div>\n";
		}
		?>
		<div class='suf-border-options'>
			<p>For any edge set style to "No Border" if you don't want a border.</p>
			<table class='opt-sub-table-5'>
				<col class='opt-sub-table-col-51'/>
				<col class='opt-sub-table-col-5'/>
				<col class='opt-sub-table-col-5'/>
				<col class='opt-sub-table-col-5'/>
				<col class='opt-sub-table-col-5'/>

				<tr>
					<th scope="col">&nbsp;</th>
					<th scope="col">Border Style</th>
					<th scope="col">Color</th>
					<th scope="col">Border Width</th>
					<th scope="col">Border Width Units</th>
				</tr>

				<?php foreach ($edges as $edge => $edge_text): ?>
					<tr>
						<th scope="row"><?php echo esc_html($edge_text); ?> border</th>
						<td valign='top'>
							<select name="<?php echo esc_attr($value['id'] . '-' . $edge); ?>-style" 
									id="<?php echo esc_attr($value['id'] . '-' . $edge); ?>-style">
								<?php foreach ($styles as $option_value => $option_text): ?>
									<option value="<?php echo esc_attr($option_value); ?>" 
											<?php selected($default[$edge]['style'] ?? '', $option_value); ?>>
										<?php echo esc_html($option_text); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>

						<td valign='top'>
							<div class="color-picker-group">
								<input type="radio" name="<?php echo esc_attr($value['id'] . '-' . $edge); ?>-colortype" 
									   value="transparent" <?php checked($default[$edge]['colortype'] ?? '', 'transparent'); ?> /> 
								Transparent / No color<br/>
								<input type="radio" name="<?php echo esc_attr($value['id'] . '-' . $edge); ?>-colortype" 
									   value="custom" <?php checked($default[$edge]['colortype'] ?? '', 'custom'); ?>/> Custom
								<input type="text" id="<?php echo esc_attr($value['id'] . '-' . $edge); ?>-color" 
									   name="<?php echo esc_attr($value['id']); ?>-color" 
									   value="<?php echo esc_attr($default[$edge]['color'] ?? ''); ?>" class="color" /><br />
								Default: <span><?php echo esc_html($original[$edge]['color']); ?></span>
							</div>
						</td>

						<td valign='top'>
							<input type="text" id="<?php echo esc_attr($value['id'] . '-' . $edge); ?>-border-width" 
								   name="<?php echo esc_attr($value['id'] . '-' . $edge); ?>-border-width" 
								   value="<?php echo esc_attr($default[$edge]['border-width'] ?? ''); ?>" /><br />
						</td>

						<td valign='top'>
							<select name="<?php echo esc_attr($value['id'] . '-' . $edge); ?>-border-width-type" 
									id="<?php echo esc_attr($value['id'] . '-' . $edge); ?>-border-width-type">
								<?php foreach ($border_width_units as $option_value => $option_text): ?>
									<option value="<?php echo esc_attr($option_value); ?>" 
											<?php selected($default[$edge]['border-width-type'] ?? '', $option_value); ?>>
										<?php echo esc_html($option_text); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<input type='hidden' id="<?php echo esc_attr($value['id']); ?>" 
				   name="suffusion_options[<?php echo esc_attr($value['id']); ?>]" 
				   value="<?php echo esc_attr($default_txt); ?>" />
		</div>
		<?php
		$this->create_closing_tag($value);
	}

	/**
	 * Renders an option whose type is "font". Invoked by add_settings_field.
	 */
	public function create_section_for_font(array $value): void {
		global $suffusion_options, $suffusion_safe_font_faces;
		$this->create_opening_tag($value);
		
		$original = $value['std'];
		if (!isset($suffusion_options[$value['id']])) {
			$default = $value['std'];
			$default_txt = implode(';', array_map(
				fn($opt, $opt_val) => "$opt=" . stripslashes($opt_val),
				array_keys($value['std']),
				$value['std']
			));
		} else {
			$default_txt = $suffusion_options[$value['id']];
			if (is_array($default_txt)) {
				$default = $default_txt;
				$default_txt = implode(';', array_map(
					fn($opt, $opt_val) => "$opt=" . stripslashes($opt_val),
					array_keys($default),
					$default
				));
			}
			$default = $default_txt;
			$default = stripslashes($default);
			$default = wp_specialchars_decode($default, ENT_QUOTES);
			$vals = explode(';', $default);
			$default = [];
			foreach ($vals as $val) {
				$pair = explode('=', $val);
				if (isset($pair[0], $pair[1])) {
					$default[$pair[0]] = stripslashes($pair[1]);
				} elseif (isset($pair[0])) {
					$default[$pair[0]] = '';
				}
			}
		}

		$exclude = $value['exclude'] ?? [];

		$font_size_types = [
			'pt' => 'Points (pt)',
			'px' => 'Pixels (px)',
			'%' => 'Percentages (%)',
			'em' => 'Em'
		];
		
		$font_styles = [
			'normal' => 'Normal',
			'italic' => 'Italic',
			'oblique' => 'Oblique',
			'inherit' => 'Inherit'
		];
		
		$font_variants = [
			'normal' => 'Normal',
			'small-caps' => 'Small Caps',
			'inherit' => 'Inherit'
		];
		
		$font_weights = [
			'normal' => 'Normal',
			'bold' => 'Bold',
			'bolder' => 'Bolder',
			'lighter' => 'Lighter',
			'inherit' => 'Inherit'
		];
		?>
		<div class='suf-font-options'>
			<table class='opt-sub-table'>
				<col class='opt-sub-table-cols'/>
				<col class='opt-sub-table-cols'/>
				<tr>
				<?php if (!in_array('font-color', $exclude, true)): ?>
					<td valign='top'>
						<div class="color-picker-group">
							<strong>Font Color:</strong><br />
							<input type="text" id="<?php echo esc_attr($value['id']); ?>-color" 
								   name="<?php echo esc_attr($value['id']); ?>-color" 
								   value="<?php echo esc_attr($default['color']); ?>" class="color" /><br />
							Default: <span><?php echo esc_html($original['color']); ?></span>
						</div>
					</td>
				<?php endif; ?>
				
				<?php if (!in_array('font-face', $exclude, true)): ?>
					<td valign='top'>
						<strong>Font Face:</strong><br />
						<select name="<?php echo esc_attr($value['id']); ?>-font-face" 
								id="<?php echo esc_attr($value['id']); ?>-font-face">
							<?php foreach ($suffusion_safe_font_faces as $option_value => $option_text): ?>
								<option value="<?php echo esc_attr(stripslashes($option_value)); ?>" 
										<?php selected(stripslashes($default['font-face']), stripslashes($option_value)); ?>>
									<?php echo esc_html($option_value); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				<?php endif; ?>
				</tr>

				<?php if (!in_array('font-size', $exclude, true)): ?>
					<tr>
						<td valign='top'>
							<strong>Font Size:</strong><br />
							<input type="text" id="<?php echo esc_attr($value['id']); ?>-font-size" 
								   name="<?php echo esc_attr($value['id']); ?>-font-size" 
								   value="<?php echo esc_attr($default['font-size']); ?>" />
						</td>
						<td valign='top'>
							<strong>Font Size Type:</strong><br />
							<select name="<?php echo esc_attr($value['id']); ?>-font-size-type" 
									id="<?php echo esc_attr($value['id']); ?>-font-size-type">
								<?php foreach ($font_size_types as $option_value => $option_text): ?>
									<option value="<?php echo esc_attr($option_value); ?>" 
											<?php selected($default['font-size-type'], $option_value); ?>>
										<?php echo esc_html($option_text); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				<?php endif; ?>

				<tr>
				<?php if (!in_array('font-style', $exclude, true)): ?>
					<td valign='top'>
						<strong>Font Style:</strong><br />
						<select name="<?php echo esc_attr($value['id']); ?>-font-style" 
								id="<?php echo esc_attr($value['id']); ?>-font-style">
							<?php foreach ($font_styles as $option_value => $option_text): ?>
								<option value="<?php echo esc_attr($option_value); ?>" 
										<?php selected($default['font-style'], $option_value); ?>>
									<?php echo esc_html($option_text); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				<?php endif; ?>
				
				<?php if (!in_array('font-variant', $exclude, true)): ?>
					<td valign='top'>
						<strong>Font Variant:</strong><br />
						<select name="<?php echo esc_attr($value['id']); ?>-font-variant" 
								id="<?php echo esc_attr($value['id']); ?>-font-variant">
							<?php foreach ($font_variants as $option_value => $option_text): ?>
								<option value="<?php echo esc_attr($option_value); ?>" 
										<?php selected($default['font-variant'], $option_value); ?>>
									<?php echo esc_html($option_text); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				<?php endif; ?>
				</tr>

				<?php if (!in_array('font-weight', $exclude, true)): ?>
					<tr>
						<td valign='top' colspan='2'>
							<strong>Font Weight:</strong><br />
							<select name="<?php echo esc_attr($value['id']); ?>-font-weight" 
									id="<?php echo esc_attr($value['id']); ?>-font-weight">
								<?php foreach ($font_weights as $option_value => $option_text): ?>
									<option value="<?php echo esc_attr($option_value); ?>" 
											<?php selected($default['font-weight'], $option_value); ?>>
										<?php echo esc_html($option_text); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				<?php endif; ?>
			</table>
			<input type='hidden' id="<?php echo esc_attr($value['id']); ?>" 
				   name="suffusion_options[<?php echo esc_attr($value['id']); ?>]" 
				   value="<?php echo esc_attr(stripslashes($default_txt)); ?>" />
		</div>
		<?php
		$this->create_closing_tag($value);
	}

	/**
	 * Renders an option whose type is "associative-array". Invoked by add_settings_field.
	 */
	public function create_section_for_associative_array(array $value): void {
		global $suffusion_options;
		$this->create_opening_tag($value);

		$stored_value = $suffusion_options[$value['id']] ?? $value['std'];
		$stored_value = suffusion_get_associative_array($stored_value);

		if (!isset($value['options']) || !is_array($value['options'])) {
			$this->create_closing_tag($value);
			return;
		}

		$associative_array = $value['options'];
		$total = count($associative_array);
		
		if ($total === 0) {
			$this->create_closing_tag($value);
			return;
		}

		echo "<table class='suf-associative-array-options opt-sub-table opt-sub-table-" . ($total + 1) . "'>\n";
		echo "<col class='opt-sub-table-col-" . ($total + 1) . "1'/>";
		
		for ($i = 1; $i < $total + 1; $i++) {
			echo "<col class='opt-sub-table-col-" . ($total + 1) . "'/>";
		}

		$key_values = [];
		$associations = [];
		$column_keys = [];
		
		echo "<tr>\n";
		echo "<th>#</th>\n";
		
		$counter = 0;
		foreach ($associative_array as $header => $details) {
			$counter++;
			echo "<th>" . esc_html($header) . "</th>\n";
			
			if ($counter === 1) {
				$key_values = $details;
			} else {
				$associations[] = $details;
				$column_keys[] = $details['name'];
			}
		}
		echo "</tr>\n";

		$counter = 0;
		$key_value_list = [];

		foreach ($key_values as $key => $key_value) {
			$counter++;
			$stored_associations = $stored_value[$key] ?? [];
			
			echo "<tr>\n";
			echo "<td valign='top'>" . esc_html($counter) . "</td>\n";
			echo "<td valign='top'>" . esc_html($key_value) . "</td>\n";
			
			$assoc_list = [];
			foreach ($associations as $association) {
				echo "<td valign='top'>\n";
				$to_check = $stored_associations[$association['name']] ?? '';

				match ($association['type']) {
					'select' => $this->render_select_field($value['id'], $key, $association, $to_check),
					'text' => $this->render_text_field($value['id'], $key, $association, $to_check),
					'multi-select' => $this->render_multi_select_field($value['id'], $key, $association, $to_check),
					default => null,
				};

				echo "</td>\n";
				$assoc_list[] = $association['name'] . '=' . $to_check;
			}
			
			$assoc_str = implode(';', $assoc_list);
			$key_value_list[] = $key . '::' . $assoc_str;
			echo "</tr>\n";
		}
		
		echo "</table>\n";
		$key_value_str = implode('||', $key_value_list);
		
		echo "<input type='hidden' id='" . esc_attr($value['id']) . "-rows' value='" . 
			 esc_attr(implode(',', array_keys($key_values))) . "'/>";
		echo "<input type='hidden' id='" . esc_attr($value['id']) . "-columns' value='" . 
			 esc_attr(implode(',', $column_keys)) . "'/>";
		echo "<input type='hidden' id='" . esc_attr($value['id']) . "' value='" . 
			 esc_attr($key_value_str) . "' name='suffusion_options[" . esc_attr($value['id']) . "]' />";

		$this->create_closing_tag($value);
	}

	/**
	 * Renders a select field for the associative array.
	 */
	private function render_select_field(string $id, string $key, array $association, string $to_check): void {
		$option_list = apply_filters('suffusion_admin_modify_option_list', $association['options'], $id, $association['name']);
		
		echo "<select name='" . esc_attr($id) . "-$key-{$association['name']}' id='" . 
			 esc_attr($id) . "-$key-{$association['name']}' >\n";
		
		foreach ($option_list as $choice_key => $choice_text) {
			echo "<option value='" . esc_attr($choice_key) . "' " . 
				 selected($to_check, $choice_key, false) . ">" . 
				 esc_html($choice_text) . "</option>";
		}
		echo "</select>\n";
	}

	/**
	 * Renders a text field for the associative array.
	 */
	private function render_text_field(string $id, string $key, array $association, string $to_check): void {
		echo "<input name='" . esc_attr($id) . "-$key-{$association['name']}' id='" . 
			 esc_attr($id) . "-$key-{$association['name']}' type='text' value='" . 
			 esc_attr($to_check) . "'/>\n";
	}

	/**
	 * Renders a multi-select field for the associative array.
	 */
	private function render_multi_select_field(string $id, string $key, array $association, string $to_check): void {
		$checkboxes = explode(',', $to_check);
		foreach ($association['options'] as $choice_key => $choice_text) {
			echo "<label><input type='checkbox' name='" . esc_attr($id) . "-$key-{$association['name']}[$choice_key]' " . 
				 "id='" . esc_attr($id) . "-$key-{$association['name']}[$choice_key]' " . 
				 checked(in_array($choice_key, $checkboxes, true), true, false) . ">" . 
				 esc_html($choice_text) . "</label><br/>\n";
		}
	}

	/**
	 * Renders an option whose type is "blurb". Invoked by add_settings_field.
	 */
	public function create_section_for_blurb(array $value): void {
		$this->create_opening_tag($value);
		$this->create_closing_tag($value);
	}

	/**
	 * Renders an option whose type is "button". Invoked by add_settings_field.
	 */
	public function create_section_for_button(array $value): void {
		$this->create_opening_tag($value);
		$category = $value['parent'];
		echo "<input name=\"suffusion_options[submit-$category]\" type='submit' value=\"" . 
			 esc_attr($value['std']) . "\" class=\"button\" />\n";
		$this->create_closing_tag($value);
	}

	/**
	 * Takes the flat options array and converts it into a hierarchical array, with the root level, 
	 * and subsequent nested levels.
	 */
	public function get_option_structure(): array {
		if (isset($this->option_structure)) {
			return $this->option_structure;
		}

		$options = $this->options;
		$option_structure = [];
		$nested_options = [];

		foreach ($options as $value) {
			match ($value['type']) {
				'title' => $this->process_title_option($value, $option_structure),
				'sub-section-2', 'sub-section-3' => $this->process_subsection_option($value, $option_structure, $nested_options),
				default => $this->process_default_option($value, $option_structure, $nested_options),
			};
		}

		$this->option_structure = $option_structure;
		$this->nested_options = $nested_options;
		return $option_structure;
	}

	/**
	 * Process a title option for the option structure.
	 */
	private function process_title_option(array $value, array &$option_structure): void {
		$option_structure[$value['category']] = [
			'slug' => $value['category'],
			'name' => $value['name'],
			'children' => [],
			'parent' => null,
		];
	}

	/**
	 * Process a subsection option for the option structure.
	 */
	private function process_subsection_option(array $value, array &$option_structure, array &$nested_options): void {
		$option_structure[$value['parent']]['children'][$value['category']] = $value['name'];

		$option_structure[$value['category']] = [
			'slug' => $value['category'],
			'name' => $value['name'],
			'children' => [],
			'parent' => $value['parent'],
		];

		if (isset($value['help'])) {
			$option_structure[$value['category']]['help'] = $value['help'];
		}
		if (isset($value['buttons'])) {
			$option_structure[$value['category']]['buttons'] = $value['buttons'];
		}

		if ($value['type'] === 'sub-section-3') {
			$nested_options[$value['category']] = [];
		}
	}

	/**
	 * Process a default option for the option structure.
	 */
	private function process_default_option(array $value, array &$option_structure, array &$nested_options): void {
		$option_structure[$value['parent']]['children'][$value['name']] = $value['name'];
		if (isset($value['id'])) {
			$nested_options[$value['parent']][] = $value['id'];
		}
	}

	/**
	 * Creates the HTML markup for the page that shows up for a sub-menu page.
	 */
	public function get_options_html_for_group(array $option_structure, string $group): void {
		echo "<div class='suf-options suf-options-$group' id='suf-options'>";
		$this->render_page_header($option_structure, $group);
		$this->render_section_tabs($option_structure, $group);
		$this->render_settings_sections($option_structure, $group);
		echo "</div><!-- /#suf-options -->\n";
	}

	/**
	 * Renders the page header for the options group.
	 */
	private function render_page_header(array $option_structure, string $group): void {
		echo "<div class='suf-options-page-header fix'>\n";
		foreach ($option_structure as $l1) {
			if (!isset($l1['parent']) || $l1['parent'] === null) {
				foreach ($l1['children'] as $l2slug => $l2name) {
					if ($group === $l2slug) {
						echo "<h1>" . esc_html($l2name) . "</h1>\n";
						echo "<div id='search-area'>" . 
							 "<label for='quick-search'>Quick Search: </label>" . 
							 "<input type='text' id='quick-search' />" . 
							 "<div id='search-match'></div>" . 
							 "</div>\n";
					}
				}
			}
		}
		echo "</div><!-- suf-options-page-header -->\n";
	}

	/**
	 * Renders the section tabs for the options group.
	 */
	private function render_section_tabs(array $option_structure, string $group): void {
		echo "<ul id='suf-section-tabs-$group' class='suf-section-tabs'>";
		foreach ($option_structure as $l1) {
			if (!isset($l1['parent']) || $l1['parent'] === null) {
				foreach ($l1['children'] as $l2slug => $l2name) {
					if ($group === $l2slug) {
						foreach ($option_structure[$l2slug]['children'] as $l3slug => $l3name) {
							echo "<li><a href='#" . esc_attr($l3slug) . "'>" . 
								 esc_html($l3name) . "</a></li>\n";
						}
					}
				}
			}
		}
		echo "</ul>";
	}

	/**
	 * Renders the settings sections for the options group.
	 */
	private function render_settings_sections(array $option_structure, string $group): void {
		foreach ($option_structure as $option) {
			if (isset($option['parent']) && $option['parent'] === 'root' && $option['slug'] === $group) {
				do_settings_sections($this->file);
				echo "</form>\n";
				echo "</div><!-- main-content -->\n";
			}
		}
	}

	/**
	 * Makes calls to add_settings_field for different types of options.
	 */
	public function add_settings_fields(string $section, string $parent): void {
		$filtered_options = $this->get_sections_for_submenu($section);
		$ctr = 0;
		
		foreach ($filtered_options as $value) {
			if (isset($value['conditional']) && $value['conditional'] === true) {
				$show = true;
				if (isset($value['conditions'])) {
					$show = $this->evaluate_conditions($value['conditions']);
				}
				if (!$show) {
					continue;
				}
			}

			$ctr++;
			$field_id = isset($value['id']) ? $value['id'] : "{$section}-{$ctr}";
			
			match ($value['type']) {
				'title' => add_settings_field('', '', [$this, 'create_title'], $parent, $section, $value),
				'sub-section-2' => add_settings_field('', '', [$this, 'create_suf_header_2'], $parent, $section, $value),
				'sub-section-3' => add_settings_field('', '', [$this, 'create_suf_header_3'], $parent, $section, $value),
				'sub-section-4' => add_settings_field($field_id, '', [$this, 'create_suf_grouping'], $parent, $section, $value),
				'text' => add_settings_field($field_id, '', [$this, 'create_section_for_text'], $parent, $section, $value),
				'textarea' => add_settings_field($field_id, '', [$this, 'create_section_for_textarea'], $parent, $section, $value),
				'select' => add_settings_field($field_id, '', [$this, 'create_section_for_select'], $parent, $section, $value),
				'multi-select' => add_settings_field($field_id, '', [$this, 'create_section_for_multi_select'], $parent, $section, $value),
				'radio' => add_settings_field($field_id, '', [$this, 'create_section_for_radio'], $parent, $section, $value),
				'checkbox' => add_settings_field($field_id, '', [$this, 'create_section_for_checkbox'], $parent, $section, $value),
				'color-picker' => add_settings_field($field_id, '', [$this, 'create_section_for_color_picker'], $parent, $section, $value),
				'upload' => add_settings_field($field_id, '', [$this, 'create_section_for_upload'], $parent, $section, $value),
				'sortable-list' => add_settings_field($field_id, '', [$this, 'create_section_for_sortable_list'], $parent, $section, $value),
				'slider' => add_settings_field($field_id, '', [$this, 'create_section_for_slider'], $parent, $section, $value),
				'background' => add_settings_field($field_id, '', [$this, 'create_section_for_background'], $parent, $section, $value),
				'border' => add_settings_field($field_id, '', [$this, 'create_section_for_border'], $parent, $section, $value),
				'font' => add_settings_field($field_id, '', [$this, 'create_section_for_font'], $parent, $section, $value),
				'associative-array' => add_settings_field($field_id, '', [$this, 'create_section_for_associative_array'], $parent, $section, $value),
				'blurb' => add_settings_field($field_id, '', [$this, 'create_section_for_blurb'], $parent, $section, $value),
				'button' => add_settings_field($field_id, '', [$this, 'create_section_for_button'], $parent, $section, $value),
				default => null,
			};
		}
	}

	/**
	 * Controls the display of conditional fields.
	 */
	public function evaluate_conditions(array $conditions): bool {
		$operator = $conditions['operator'] ?? 'OR';
		$nested_conditions = $conditions['conditions'];
		
		if (isset($nested_conditions['operator'])) {
			return $this->evaluate_conditions($nested_conditions);
		}
		
		if ($operator === 'NOT') {
			$variable = key($nested_conditions);
			$check_value = current($nested_conditions);
			$suf_variable = "suf_$variable";
			global $$suf_variable;
			return $$suf_variable !== $check_value;
		}
		
		$evals = [];
		foreach ($nested_conditions as $variable => $check_value) {
			$suf_variable = "suf_$variable";
			global $$suf_variable;
			$evals[] = $$suf_variable === $check_value ? 1 : 0;
		}
		
		return $this->array_join_boolean($evals, $operator);
	}

	/**
	 * Joins boolean conditions based on the operator.
	 */
	private function array_join_boolean(array $conditions, string $operator): bool {
		if (count($conditions) === 1) {
			return (bool)$conditions[0];
		}

		$first = $conditions[0];
		$rest = array_slice($conditions, 1);
		
		return match ($operator) {
			'AND' => ($first * $this->array_join_boolean($rest, $operator)) !== 0,
			'NOR' => ($first + $this->array_join_boolean($rest, $operator)) === 0,
			'NAND' => ($first * $this->array_join_boolean($rest, $operator)) === 0,
			default => ($first + $this->array_join_boolean($rest, $operator)) !== 0, // OR
		};
	}

	/**
	 * Top level rendering call for a sub-menu page. This in turn invokes the rendering calls for individual sections (tabs) within
	 * a sub-menu page.
	 */
	public function get_options_html(?array $option_structure = null): void {
		$option_structure ??= $this->get_option_structure();

		foreach ($option_structure as $l1) {
			if (!isset($l1['parent']) || $l1['parent'] === null) {
				foreach ($l1['children'] as $l2slug => $l2name) {
					$this->get_options_html_for_group($option_structure, $l2slug);
				}
			}
		}
	}

	/**
	 * Registers settings, then adds individual settings sections and their fields to the queue for rendering. The result of this
	 * is used by do_settings_sections.
	 */
	public function initialize_settings(?array $structure = null): void {
		$structure ??= $this->get_option_structure();

		foreach ($structure as $option_entity) {
			if (!isset($option_entity['parent'])) {
				continue; // Root node, skip
			}

			if ($option_entity['parent'] === 'root') {
				// Sub-menu options already registered
				continue;
			}

			if (isset($option_entity['parent'])) {
				// Section under current sub-menu
				register_setting(
					"suffusion-options-{$option_entity['slug']}", 
					'suffusion_options', 
					[$this, 'validate_options']
				);
				
				add_settings_section(
					$option_entity['slug'], 
					'', 
					[$this, 'create_settings_section'], 
					$this->file
				);
				
				$this->add_settings_fields($option_entity['slug'], $this->file);
			}
		}
	}

	/**
	 * Validates the inputs provided by users.
	 * 1. Text type options are checked for special characters
	 * 2. Radio/select items are checked against the master list
	 * 3. Multi-select and sortable-list fields are checked against the master list
	 */
	public function validate_options(array $options): array {
		foreach ($options as $option => $option_value) {
			if (!isset($this->reverse_options[$option])) {
				continue;
			}

			// Sanitize options based on type
			$options[$option] = match ($this->reverse_options[$option]) {
				'text', 'textarea', 'slider', 'color-picker', 'background', 
				'border', 'font', 'upload', 'template', 'associative-array' => 
					esc_attr($option_value),

				'select', 'radio' => $this->validate_select_option($option, $option_value),

				'multi-select' => $this->validate_multi_select_option($option, $option_value),

				'sortable-list' => $this->validate_sortable_list_option($option, $option_value),

				'checkbox' => $this->validate_checkbox_option($option, $option_value),

				default => $option_value,
			};
		}

		$options = $this->process_hidden_options($options);
		$options = $this->process_nested_options($options);
		
		$options['theme-version'] = SUFFUSION_THEME_VERSION;
		$options['option-date'] = date(get_option('date_format') . ' ' . get_option('time_format'));
		
		return array_merge(suffusion_default_options(), $options);
	}

	/**
	 * Creates a settings section with proper markup.
	 */
	public function create_settings_section(array $section): void {
		$option_structure = $this->option_structure;
		
		if ($this->displayed_sections !== 0) {
			echo "</form>\n</div><!-- main-content -->\n";
		}

		echo "<div id='{$option_structure[$section['id']]['slug']}' class='suffusion-options-panel'>\n";
		echo "<form method='post' action='options.php' id='suffusion-options-form-{$section['id']}' class='suffusion-options-form'>\n";
		echo '<h3>' . esc_html($option_structure[$section['id']]['name']) . "</h3>\n";

		// Pass page parameter to options.php to prevent "Options page not found" error
		$page = $_REQUEST['page'] ?? '';
		$tab = $_REQUEST['tab'] ?? 'theme-options-intro.php';
		
		echo "<input type='hidden' name='page' value='" . esc_attr($page) . "' />\n";
		echo "<input type='hidden' name='tab' value='" . esc_attr($tab) . "' />\n";

		settings_fields("suffusion-options-{$section['id']}");
		
		$this->render_button_bar($section['id'], $option_structure);
		
		$this->displayed_sections++;
		$this->previous_displayed_section = $section['id'];
	}

	/**
	 * Migrates options from version 3.0.2 or lower.
	 */
	public function migrate_from_v302(array $options): array {
		global $suffusion_inbuilt_options;
		if (!isset($suffusion_inbuilt_options) || !is_array($suffusion_inbuilt_options)) {
			require_once(get_template_directory() . '/admin/theme-options.php');
		}

		foreach ($suffusion_inbuilt_options as $option => $value) {
			if (isset($value['type']) && $value['type'] === 'multi-select') {
				$allowed = $value['options'];
				$new_value = [];
				foreach ($allowed as $idx => $idx_value) {
					$spawn = $value['id'] . '_' . $idx;
					if (get_option($spawn)) {
						$new_value[] = $idx;
					}
				}
				$options[$value['id']] = implode(',', $new_value);
			}
		}

		// Migrate meta fields
		$meta_fields = ['suf_alt_page_title' => 'text'];
		$pages = get_pages();
		
		if ($pages && is_array($pages)) {
			foreach ($pages as $page) {
				$page_id = $page->ID;
				if ($page === null) {
					continue;
				}
				
				foreach ($meta_fields as $meta_field => $type) {
					$data = $type === 'checkbox' ? 'on' : get_option($meta_field . '_' . $page_id);
					$this->update_post_meta_if_needed($page_id, $meta_field, $data);
				}
			}
		}

		return $options;
	}

	/**
	 * Migrates options from version 3.4.3 or lower.
	 */
	public function migrate_from_v343(array $options): array {
		global $suffusion_inbuilt_options;
		if (!isset($suffusion_inbuilt_options) || !is_array($suffusion_inbuilt_options)) {
			require_once(get_template_directory() . '/admin/theme-options.php');
		}

		foreach ($suffusion_inbuilt_options as $value) {
			if (isset($value['id'])) {
				$option_value = get_option($value['id']);
				if ($option_value === false) {
					unset($options[$value['id']]);
				} else {
					$options[$value['id']] = $option_value;
				}
			}
		}

		return $options;
	}

	/**
	 * Exports settings as a PHP file.
	 */
	public function export_settings(string $what = 'all'): void {
		global $suffusion_inbuilt_options, $suffusion_unified_options;
		$export = [];
		
		if (!isset($suffusion_inbuilt_options) || !is_array($suffusion_inbuilt_options)) {
			require_once(get_template_directory() . '/admin/theme-options.php');
		}
		
		foreach ($suffusion_inbuilt_options as $value) {
			if ((!isset($value['export']) || $value['export'] !== 'ne' || $what === 'all') && 
				isset($value['id']) && $value['type'] !== 'button') {
				$export[$value['id']] = $suffusion_unified_options[$value['id']] ?? $value['std'];
			}
		}
		
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename="suffusion-options.php"');
		
		echo "<?php\n";
		echo "/* Suffusion settings exported on " . date('Y-m-d H:i') . " */\n";
		echo '$suffusion_exported_options = ';
		var_export($export);
		echo ";\n?>";
		die;
	}

	/**
	 * Imports settings from a file exported by export_settings().
	 * The import file must be in the "import" folder under "admin" in the "suffusion" directory.
	 */
	public function import_settings(array $options): array {
		global $suffusion_exported_options;
		$template_path = get_template_directory();
		$import_file = $template_path . '/admin/import/suffusion-options.php';

		if (!file_exists($import_file)) {
			return $options;
		}

		// Include the file with imported options
		require_once $import_file;

		if (!isset($suffusion_exported_options) || !is_array($suffusion_exported_options)) {
			return $options;
		}

		// Merge imported options with existing ones
		foreach ($suffusion_exported_options as $option => $option_value) {
			$options[$option] = $option_value;
		}

		return $options;
	}
}
?>
