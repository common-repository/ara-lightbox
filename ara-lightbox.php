<?php

/*
 * Plugin Name: Ara Lightbox
 * Plugin URI:
 * Description: An easy lightbox plugin. It shows your picture in a lightbox instead of picture file.
 * Version: 1.1
 * Author: François Yerg
 * Author URI: http://me.francoisyerg.net
 * Licence: GPL2
 */

class ara_lightbox_plugin
{
	public $options; // Plugin options
	
	public function __construct()
	{
		add_action('plugins_loaded', array($this, 'load_textdomain'));
		
		$this->init_options();
		
		if (is_admin())
		{ // admin actions
			add_action('admin_menu', array($this, 'display_admin_menu')); // Insert admin menu
			add_action('admin_init', array($this, 'register_and_build_fields')); // Register fields
		}
		else
		{ // Front actions
			if ($this->options['enable'] == 'on')
			{ // If lightbox is enabled
				add_action('the_content', array($this, 'display_block'));
				wp_enqueue_style('ara-lightbox-style', plugin_dir_url(__FILE__).'/css/style.css');
				wp_enqueue_script('ara-lightbox-script', plugin_dir_url(__FILE__).'js/lightbox.js', array('jquery'), '1.0', true);
			}
		}
	}
	
	// Display front end code
	public function display_block($content)
	{
		$caption = "<div id='ara-lightbox-caption' style='color:{$this->options['capcolor']};'></div>";
		$bgopacity = $this->options['bgopacity'] / 100;
		
		$content .= "
			<div id='ara-lightbox-box' style='background-color: rgba(".implode(',', $this->hex2rgb($this->options['bgcolor'])).",{$bgopacity});'>
				".($this->options['capposition'] == 'top' ? $caption : '')."
				<img src='#' alt='#' id='ara-lightbox-img' style='border: {$this->options['imgbordersize']}px {$this->options['imgborderstyle']} {$this->options['imgbordercolor']};' />
				".($this->options['capposition'] == 'bottom' ? $caption : '')."
			</div>
			<div class='ara-lightbox-fix'></div>
		";
		
		return $content;
	}
	
	public function build_options_page()
	{
		?>
			<div id="wrap">
				<h1><?php _e('Ara Lightbox settings', 'ara-lightbox'); ?></h1>
				<form method="post" action="options.php" enctype="multipart/form-data">
					<?php settings_fields('ara_lightbox_options'); ?>
					<?php do_settings_sections(__FILE__); ?>
					<?php submit_button(); ?>
				</form>
			</div>
		<?php
	}
	
	// Options validation
	function validate_setting($theme_options) {
		return $theme_options;
	}
	
	// Register and build settings form fields
	public function register_and_build_fields()
	{
		register_setting('ara_lightbox_options', 'ara_lightbox_options', array($this, 'validate_setting'));
		
		// General section
		add_settings_section('general_settings', '', array($this, 'section_general'), __FILE__);
		add_settings_field('enable', __("Enable lightbox", 'ara-lightbox'), array($this, 'enable_setting'), __FILE__, 'general_settings');
		
		// Background section
		add_settings_section('background_settings', __("Background settings", 'ara-lightbox'), array($this, 'section_background'), __FILE__);
		add_settings_field('bgcolor', __("Background color", 'ara-lightbox'), array($this, 'bgcolor_setting'), __FILE__, 'background_settings');
		add_settings_field('bgopacity', __("Background opacity level", 'ara-lightbox'), array($this, 'bgopacity_setting'), __FILE__, 'background_settings');
		
		// Image section
		add_settings_section('image_settings', __("Image settings", 'ara-lightbox'), array($this, 'section_image'), __FILE__);
		add_settings_field('imgbordersize', __("Image border size (px)", 'ara-lightbox'), array($this, 'imgbordersize_setting'), __FILE__, 'image_settings');
		add_settings_field('imgborderstyle', __("Image border style", 'ara-lightbox'), array($this, 'imgborderstyle_setting'), __FILE__, 'image_settings');
		add_settings_field('imgbordercolor', __("Image border color", 'ara-lightbox'), array($this, 'imgbordercolor_setting'), __FILE__, 'image_settings');
		
		// Caption section
		add_settings_section('caption_settings', __("Caption settings", 'ara-lightbox'), array($this, 'section_caption'), __FILE__);
		add_settings_field('capposition', __("caption position", 'ara-lightbox'), array($this, 'capposition_setting'), __FILE__, 'caption_settings');
		add_settings_field('capcolor', __("caption color", 'ara-lightbox'), array($this, 'capcolor_setting'), __FILE__, 'caption_settings');
	}
	
	public function section_general() {}
	
	public function section_background() {}
	
	public function section_image() {}
	
	public function section_caption() {}
	
	public function enable_setting() {
		echo "<input name='ara_lightbox_options[enable]' type='checkbox'".($this->options['enable'] == 'on' ? ' checked' : '')." />";
	}
	
	public function bgcolor_setting() {
		echo "<input name='ara_lightbox_options[bgcolor]' type='text' class='color-field' value='{$this->options['bgcolor']}' required />";
	}
	
	public function bgopacity_setting() {
		echo "<input name='ara_lightbox_options[bgopacity]' type='range' min='0' max='100' step='5' value='{$this->options['bgopacity']}' required />";
	}
	
	public function imgbordersize_setting() {
		echo "<input name='ara_lightbox_options[imgbordersize]' type='number' min='0' value='{$this->options['imgbordersize']}' required />";
	}
	
	public function imgborderstyle_setting()
	{
		echo "<select name='ara_lightbox_options[imgborderstyle]' required>
				<option value='solid'".($this->options['imgborderstyle'] == 'solid' ? ' selected' : '').">".__("Solid", 'ara-lightbox')."</option>
				<option value='dashed'".($this->options['imgborderstyle'] == 'dashed' ? ' selected' : '').">".__("Dashed", 'ara-lightbox')."</option>
				<option value='dotted'".($this->options['imgborderstyle'] == 'dotted' ? ' selected' : '').">".__("Dotted", 'ara-lightbox')."</option>
				<option value='double'".($this->options['imgborderstyle'] == 'double' ? ' selected' : '').">".__("Double", 'ara-lightbox')."</option>
				<option value='groove'".($this->options['imgborderstyle'] == 'groove' ? ' selected' : '').">".__("Groove", 'ara-lightbox')."</option>
				<option value='ridge'".($this->options['imgborderstyle'] == 'ridge' ? ' selected' : '').">".__("Ridge", 'ara-lightbox')."</option>
				<option value='inset'".($this->options['imgborderstyle'] == 'inset' ? ' selected' : '').">".__("Inset", 'ara-lightbox')."</option>
				<option value='outset'".($this->options['imgborderstyle'] == 'outset' ? ' selected' : '').">".__("Outset", 'ara-lightbox')."</option>
			</select>";
	}
	
	public function imgbordercolor_setting() {
		echo "<input name='ara_lightbox_options[imgbordercolor]' type='text' class='color-field' value='{$this->options['imgbordercolor']}' required />";
	}
	
	public function capposition_setting()
	{
		echo "<select name='ara_lightbox_options[capposition]' required>
				<option value='hidden'".($this->options['capposition'] == 'hidden' ? ' selected' : '').">".__("Hidden", 'ara-lightbox')."</option>
				<option value='bottom'".($this->options['capposition'] == 'bottom' ? ' selected' : '').">".__("Bottom", 'ara-lightbox')."</option>
				<option value='top'".($this->options['capposition'] == 'top' ? ' selected' : '').">".__("Top", 'ara-lightbox')."</option>
			</select>";
	}
	
	public function capcolor_setting() {
		echo "<input name='ara_lightbox_options[capcolor]' type='text' class='color-field' value='{$this->options['capcolor']}' required />";
	}
	
	// Display menu button
	public function display_admin_menu()
	{
		if (function_exists('add_options_page'))
		{
			$plugin_page_options = add_options_page(
				'Ara Lightbox',
				'Ara Lightbox',
				'administrator',
				'ara-lightbox',
				array($this, 'build_options_page')
			);
			
			add_action('admin_print_scripts-'.$plugin_page_options, array($this, 'load_admin_scripts'));
		}
	}
	
	public function load_admin_scripts()
	{
		wp_enqueue_style('ara-lightbox-admin-style', plugin_dir_url(__FILE__).'/css/admin.css');
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('myplugin-script', plugins_url('js/admin.js', __FILE__), array('wp-color-picker'), false, true);
	}

	public function init_options()
	{
		$defaults = array(
			'enable'			=> 'on',
			'bgcolor'			=> '#000000',
			'bgopacity'			=> 80,
			'imgbordersize'		=> 0,
			'imgborderstyle'	=> 'solid',
			'imgbordercolor'	=> '#000000',
			'capposition'		=> 'bottom',
			'capcolor'			=> '#FFFFFF',
		);
		
		$this->options = wp_parse_args(get_option('ara_lightbox_options'), $defaults);
	}
	
	public function load_textdomain() {
		load_plugin_textdomain( 'ara-lightbox', false, plugin_basename(dirname(__FILE__)).'/languages');
	}
	
	public function hex2rgb($hex)
	{
		$hex = str_replace("#", "", $hex);
		
		if(strlen($hex) == 3)
		{
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		}
		else
		{
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		
		$rgb = array($r, $g, $b);
		
		return $rgb; // returns an array with the rgb values
	}
}

new ara_lightbox_plugin;
