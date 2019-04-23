<?php
class FB_Importer_Plugin_Admin {
	const ADMIN_PAGE_TITLE = 'Facebook Importer Plugin';
	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::$initiated = true;
			self::init_hooks();
		}
	}

	public static function init_hooks() {
		add_action( 'admin_menu', array( get_class(), 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( get_class(), 'register_styles_and_scripts' ) );
	}


	public static function register_menu() {
	    $hook = add_menu_page( self::ADMIN_PAGE_TITLE, 'Import Posts', 'manage_options', 'facebook-importer', array ( get_class(), 'display_page_import_posts' ), 'dashicons-welcome-widgets-menus', 30 );
		
		// add_submenu_page( 'facebook-importer', self::ADMIN_PAGE_TITLE, 'Import Posts', 'manage_options', 'facebook-importer', array ( get_class(), 'display_page_import_posts') );

	}

	public static function register_styles_and_scripts() {
		// echo '<!-- TE_CRM_Plugin_Admin::register_styles_and_scripts -->';
		// register plugin styles
		$styles = array(
			'fb-importer-plugin-admin'				=>  plugins_url( 'css/admin.css', __FILE__ ),
		);
		
		foreach( $styles as $k => $v )
		{
			wp_register_style( $k, $v ); 
		}
		
	    wp_enqueue_style( 'fb-importer-plugin-admin' );

	}

	public static function view($name) {
		$file = FB_IMPORTER_PLUGIN_DIR . 'views/'. $name . '.php';

		include( $file );
	}

	public static function display_page_import_posts() {
		self::view('import-posts');
	}
}