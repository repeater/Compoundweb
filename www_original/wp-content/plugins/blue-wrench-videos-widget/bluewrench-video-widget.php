<?php
/*
Plugin Name:  Blue Wrench Video Widget
Plugin URI: http://bluewrenchsoftware.com/wordpress-plugins/bluewrench-video-widget/
Description: Blue Wrench Video Widget to display videos from various video sharing networks such as Vimeo, YouTube, Metacafe etc into your widget box. Simply copy the video's URL from your web browser's address bar and paste it in 'Add new video' page and add 'Blue Wrench Video Widget' from WP-Admin >> Appearance >> Widgets to desired area. Supported Networks: Vimeo, YouTube, BlipTV, Dailymotion, Veoh, Metacafe, MeFeedia and Break
Version: 2.1.0
Author: Sunil Nanda
Author URI: http://www.sunilnanda.com/

*/

/**
 * Load core files
 */
require_once(sprintf("%s/lib/BlueWrenchVideoAdmin.php", dirname(__FILE__)));
require_once(sprintf("%s/lib/BlueWrenchVideoDatabase.php", dirname(__FILE__)));
require_once(sprintf("%s/lib/BlueWrenchVideoInstaller.php", dirname(__FILE__)));
require_once(sprintf("%s/lib/BlueWrenchVideoController.php", dirname(__FILE__)));
require_once(sprintf("%s/lib/BlueWrenchVideoConstants.php", dirname(__FILE__)));
require_once(sprintf("%s/lib/BlueWrenchVideoWidget.php", dirname(__FILE__)));


/* Runs when plugin is activated */
register_activation_hook(__FILE__,array(BlueWrenchVideoInstaller::getInstance(), 'install'));
/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, array(BlueWrenchVideoInstaller::getInstance(), 'remove') );

/* Runs just before the auto upgrader installs the plugin*/
add_filter('upgrader_post_install', array(BlueWrenchVideoInstaller::getInstance(), 'upgrade'), 10, 2);

if( is_admin()){
	add_action('admin_enqueue_scripts', array(BlueWrenchVideoAdmin::getInstance(), "addAdminScripts") );	
	add_action('admin_menu', array(BlueWrenchVideoAdmin::getInstance(), "createAdminMenu"));
} else {
}

//add_action('init', array(BlueWrenchVideoShortcodeDispatcher::getInstance(), "init"));

function load_bw_video_widgets(){
	wp_register_style ( 'bw-plugin-css', plugin_dir_url( __FILE__ ) . 'bluewrench-video-widget.css' );
	wp_enqueue_style ( 'bw-plugin-css' );
	register_widget('BlueWrenchVideoWidget');

}

function fetch_video_embedd_html(){
	if (isset($_REQUEST['vid']) && $_REQUEST['vid']!=""){
		$vid = $_REQUEST['vid'];
		global $wpdb;
		$table_name = $wpdb->prefix.BlueWrenchVideoConstants::BW_TABLE_PREFIX."videos";
		$sql = "SELECT * FROM ".$table_name." where  id='".$vid."' limit 1";
		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results)>0){
			$bwMediaController = new BlueWrenchVideoController();
			$video_row = array_pop($results);
			$video_url = $video_row->value;
			$video_preview = $bwMediaController->generateEmbeddHTML($video_url);
			//bw_filter_allowed_http_origins();
			echo $video_preview;
			die();
		}
	}
}

add_action('widgets_init', 'load_bw_video_widgets');						// Action to initiate widgets
add_action('wp_ajax_fetch_video_embedd_html', 'fetch_video_embedd_html');	// AJAX Request handling

	
?>