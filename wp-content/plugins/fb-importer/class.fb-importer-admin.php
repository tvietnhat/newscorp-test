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
		add_action( "wp_ajax_fb_importer_upload_data_file", array( get_class(), 'ajax_upload_data_file' ));
	}


	public static function register_menu() {
	    $hook = add_menu_page( self::ADMIN_PAGE_TITLE, 'Facebook Import', 'manage_options', 'facebook-importer', array ( get_class(), 'display_page_import_posts' ), 'dashicons-welcome-widgets-menus', 30 );
		
		// add_submenu_page( 'facebook-importer', self::ADMIN_PAGE_TITLE, 'Import Posts', 'manage_options', 'facebook-importer', array ( get_class(), 'display_page_import_posts') );

	}

	public static function register_styles_and_scripts() {
		// echo '<!-- TE_CRM_Plugin_Admin::register_styles_and_scripts -->';
		// register plugin styles
		$styles = array(
			'fb-importer-plugin-admin'				=>  plugins_url( 'css/admin.css', __FILE__ ),
			'dropzone'								=>  plugins_url( 'css/dropzone.min.css', __FILE__ ),
		);
		
		foreach( $styles as $k => $v )
		{
			wp_register_style( $k, $v ); 
		}
		
	    wp_enqueue_style( 'fb-importer-plugin-admin' );

		wp_register_script('dropzone', plugins_url( 'js/dropzone.min.js', __FILE__ ), array('jquery'), false, true );
	}

	public static function load_view($name) {
		$file = FB_IMPORTER_PLUGIN_DIR . 'views/'. $name . '.php';

		include( $file );
	}

	public static function display_page_import_posts() {
		wp_enqueue_style('dropzone');
		wp_enqueue_script('dropzone');
		self::load_view('import-posts');
	}
	
	public static function ajax_upload_data_file() {
		$target = key_exists('target', $_REQUEST) ? $_REQUEST['target'] : null;
		// wp_send_json($upload_dir_obj);
	
		if ( isset($_FILES['file']) && !empty($target) ) {
			$upload_dir_obj = wp_upload_dir();
			$upload_dir = $upload_dir_obj['basedir'];
			$upload_dir = "{$upload_dir}/fb-importer/{$target}";
			if ( !file_exists($upload_dir) ) {
				wp_mkdir_p($upload_dir);
			}

			$file_basename = basename($_FILES['file']['name']);
			$upload_file = pathinfo($file_basename, PATHINFO_FILENAME) . '-' . time();
			$file_ext = pathinfo($file_basename, PATHINFO_EXTENSION);
			
			// make sure it's a JSON file
			if ( !empty($file_ext) && strcasecmp($file_ext, 'json') == 0 ) {
				$upload_file .= '.' . $file_ext;
			} else {
				wp_die('File must be JSON.');
				return;
			}
			
			// Store uploaded file to WP Upload folder
			$uploaded_file_absolute_path = $upload_dir . '/' . $upload_file;
			move_uploaded_file($_FILES['file']['tmp_name'], $uploaded_file_absolute_path);
		
			$upload_file_relative_url = "fb-importer/{$target}/{$upload_file}";
			$upload_file_url = $upload_dir_obj['baseurl'] . '/' . $upload_file_relative_url;

			require_once( FB_IMPORTER_PLUGIN_DIR . 'class.fb-post-importer.php' );
			$importer = new FB_Post_Importer();
			$importer->import_json($uploaded_file_absolute_path);

			wp_send_json(array( $target => $upload_file_url ));
		}
	}
	
}