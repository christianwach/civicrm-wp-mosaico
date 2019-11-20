<?php /*
--------------------------------------------------------------------------------
Plugin Name: CiviCRM WordPress Mosaico Test
Plugin URI: https://github.com/christianwach/civicrm-wp-mosaico
Description: Overrides the Mosaico template.
Author: Christian Wach
Version: 0.1
Author URI: http://haystack.co.uk
Text Domain: civicrm-wp-mosaico
Domain Path: /languages
Depends: CiviCRM
--------------------------------------------------------------------------------
*/



// Set plugin version here.
define( 'CIVICRM_WP_MOSAICO_VERSION', '0.1' );

// Store reference to this file.
if ( ! defined( 'CIVICRM_WP_MOSAICO_FILE' ) ) {
	define( 'CIVICRM_WP_MOSAICO_FILE', __FILE__ );
}

// Store URL to this plugin's directory.
if ( ! defined( 'CIVICRM_WP_MOSAICO_URL' ) ) {
	define( 'CIVICRM_WP_MOSAICO_URL', plugin_dir_url( CIVICRM_WP_MOSAICO_FILE ) );
}

// Store PATH to this plugin's directory.
if ( ! defined( 'CIVICRM_WP_MOSAICO_PATH' ) ) {
	define( 'CIVICRM_WP_MOSAICO_PATH', plugin_dir_path( CIVICRM_WP_MOSAICO_FILE ) );
}



/**
 * CiviCRM WordPress Mosaico Class.
 *
 * A class that encapsulates this plugin's functionality.
 *
 * @since 0.1
 */
class CiviCRM_WP_Mosaico {



	/**
	 * Initialises this object.
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// Register PHP and template directories.
		add_action( 'civicrm_config', array( $this, 'register_directories' ), 10 );

	}



	/**
	 * Register directories that CiviCRM searches in.
	 *
	 * @since 0.1
	 *
	 * @param object $config The CiviCRM config object.
	 */
	public function register_directories( &$config ) {

		Civi::service('dispatcher')->addListener(
			'hook_civicrm_coreResourceList',
			array( $this, 'register_php_directory' ),
			-200
		);

		Civi::service('dispatcher')->addListener(
			'hook_civicrm_coreResourceList',
			array( $this, 'register_template_directory' ),
			-200
		);

	}


	/**
	 * Register directory that CiviCRM searches in for new PHP files.
	 *
	 * This only works with *new* PHP files. One cannot override existing PHP
	 * with this technique - instead, the file must be placed in the path:
	 * defined in $config->customPHPPathDir
	 *
	 * @since 0.1
	 *
	 * @param \Civi\Core\Event\GenericHookEvent $event The event object.
	 * @param str $hook The name of the hook currently being run.
	 */
	public function register_php_directory( $event, $hook ) {

		// Kick out if no CiviCRM.
		if ( ! civi_wp()->initialize() ) return;

		// Define our custom path.
		$custom_path = CIVICRM_WP_MOSAICO_PATH . 'civicrm_php';

		// Add to include path.
		$include_path = $custom_path . PATH_SEPARATOR . get_include_path();
		set_include_path( $include_path );

	}



	/**
	 * Register directories that CiviCRM searches for template files.
	 *
	 * @since 0.1
	 *
	 * @param \Civi\Core\Event\GenericHookEvent $event The event object.
	 * @param str $hook The name of the hook currently being run.
	 */
	public function register_template_directory( $event, $hook ) {

		// Define our custom path.
		$custom_path = CIVICRM_WP_MOSAICO_PATH . 'civicrm_templates';

		// Kick out if no CiviCRM.
		if ( ! civi_wp()->initialize() ) return;

		// Get template instance.
		$template = CRM_Core_Smarty::singleton();

		// Add our custom template directory.
		$template->addTemplateDir( $custom_path );

		// Register template directories.
		$template_include_path = $custom_path . PATH_SEPARATOR . get_include_path();
		set_include_path( $template_include_path );

	}



} // Class ends.



/**
 * Load plugin if not yet loaded and return reference.
 *
 * @since 0.1
 *
 * @return CiviCRM_WP_Mosaico $civicrm_wp_mosaico The plugin reference.
 */
function civicrm_wp_mosaico() {

	// Declare as static.
	static $civicrm_wp_mosaico;

	// Instantiate plugin if not yet instantiated.
	if ( ! isset( $civicrm_wp_mosaico ) ) {
		$civicrm_wp_mosaico = new CiviCRM_WP_Mosaico();
	}

	// --<
	return $civicrm_wp_mosaico;

}

// Load only when CiviCRM has loaded.
add_action( 'civicrm_instance_loaded', 'civicrm_wp_mosaico' );



