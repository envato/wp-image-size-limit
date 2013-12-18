<?php
/*
Plugin Name: WP Image Size Limit
Plugin URI: http://wordpress.org/extend/plugins/wp-image-size-limit
Description: Allows setting a maximum file size for image uploads.
Author: Sean Butze
Author URI: http://www.seanbutze.com
Version: 1.0.4
*/


define('WPISL_DEBUG', false);

require_once ('wpisl-options.php');

class WP_Image_Size_Limit {

	public function __construct()  {  
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this, 'add_plugin_links') );
			add_filter('wp_handle_upload_prefilter', array($this, 'error_message'));
	}  

	public function add_plugin_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/options-media.php?settings-updated=true#wpisl-limit">Settings</a>'
			),
			$links
		);
	}

	public function get_limit() {
		$option = get_option('wpisl_options');

		if ( isset($option['img_upload_limit']) ){
			$limit = $option['img_upload_limit'];
		} else {
			$limit = $this->wp_limit();
		}

		return $limit;
	}

	public function output_limit() {
		$limit = $this->get_limit();
		$limit_output = $limit;
		$mblimit = $limit / 1000;


		if ( $limit >= 1000 ) {
			$limit_output = $mblimit;
		}

		return $limit_output;
	}

	public function wp_limit() {
		$output = wp_max_upload_size();
		$output = round($output);
		$output = $output / 1000000; //convert to megabytes
		$output = round($output);
		$output = $output * 1000; // convert to kilobytes

		return $output;

	}

	public function limit_unit() {
		$limit = $this->get_limit();

		if ( $limit < 1000 ) {
			return 'KB';
		}
		else {
			return 'MB';
		}

	}

	public function error_message($file) {
		$size = $file['size'];
		$size = $size / 1024;
		$type = $file['type'];
		$is_image = strpos($type, 'image');
		$limit = $this->get_limit();
		$limit_output = $this->output_limit();
		$unit = $this->limit_unit();

	  if ( ( $size > $limit ) && ($is_image !== false) ) {
	     $file['error'] = 'Image files must be smaller than '.$limit_output.$unit;
	     if (WPISL_DEBUG) {
	     	$file['error'] .= ' [ filesize = '.$size.', limit ='.$limit.' ]';
	     }
	  }
	  return $file;
	}

	public function load_styles() {
		$limit = $this->get_limit();
		$limit_output = $this->output_limit();
		$mblimit = $limit / 1000;
		$wplimit = $this->wp_limit();
		$unit = $this->limit_unit();


		?>
		<!-- .Custom Max Upload Size -->
		<style type="text/css">
		.after-file-upload {
			display: none;
		}
		<?php if ( $limit < $wplimit ) : ?>
		.upload-flash-bypass:after {
			content: 'Maximum image size: <?php echo $limit_output . $unit; ?>.';
			display: block;
			margin: 15px 0;
		}
		<?php endif; ?>

		</style>
		<!-- END Custom Max Upload Size -->
		<?php
	}


}
$WP_Image_Size_Limit = new WP_Image_Size_Limit;
add_action('admin_head', array($WP_Image_Size_Limit, 'load_styles'));


