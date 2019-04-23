<?php
/**
 * Plugin Name: Facebook Importer
 * Description: Importing Stories from Facebook to WP Posts.
 * Author: Nhat Tran
 * Version: 1.0
 * Author URI: https://tranviet.com/
 */

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}


define( 'FB_IMPORTER_VERSION', '1.0' );
define( 'FB_IMPORTER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

class FB_Importer_Plugin {
	public static function activate() {
		
	}

	public static function deactivate() {
		
	}
}

register_activation_hook( __FILE__, array( 'FB_Importer_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'FB_Importer_Plugin', 'deactivate' ) );


if ( is_admin() ) {
	require_once( FB_IMPORTER_PLUGIN_DIR . 'class.fb-importer-admin.php' );
	FB_Importer_Plugin_Admin::init();
}
