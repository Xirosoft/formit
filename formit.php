<?php
/**
 * Plugin Name: Formit
 * Description: The Ultimate drag and drop WordPress Form Builder Plugin.
 * Plugin URI: https://themeies.com/item/formit
 * Version: 1.0
 * Author: Xirosoft
 * Author URI: https://xirosoft.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: formit
 * Domain Path: /languages
 */

 

/**
 * Main Formit Plugin Class
 *
 * The init class that runs the Hello World plugin.
 * Intended To make sure that the plugin's minimum requirements are met.
 *
 * You should only modify the constants to match your plugin's needs.
 *
 * Any custom code should go inside Plugin Class in the plugin.php file.
 * @since 1.2.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once __DIR__ .'/vendor/autoload.php';
final class Formit_Plugin
{

	/**
	 * Plugin Version
	 *
	 * @since 1.2.0
	 * @var string The plugin version.
	 */
	const version = '1.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.2.0
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '5.4';


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct()
	{
		/**
		 * define constatns
		 */
		$this->define_constants(); 

		register_activation_hook( FORMIT__FILE__ , [$this, 'activate'] );

		// Load translation
		add_action('init', array($this, 'i18n'));

		// Init Plugin
		add_action('plugins_loaded', array($this, 'init'));
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 * Fired by `init` action hook.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function i18n()
	{
		load_plugin_textdomain('formit');
	}

	/**
	 * Initialize the plugin
	 *
	 * Validates that Elementor is already loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed include the plugin class.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function init()
	{
		/**
		 * Global function Innitial
		 */
		new Xirosoft\Formit\GlobalFunctions(); 
		new Xirosoft\Formit\API(); 
		new Xirosoft\Formit\Frontend\ShortCode(); 
		/**
		 * checking admin or frontend 
		 */
		if ( is_admin() ) {
			// Enqueue all admin styles and scripts
			new Xirosoft\Formit\AdminPanel(); 
		}else{
			// Manege All fontend functionility
			new Xirosoft\Formit\FrotnendPanel();
		}

	}

	/**
	 * Define the required plugin constants
	 *
	 * @return void
	 */
	public function define_constants()
	{
		define('FORMIT_VERSION', self::version);
		define('FORMIT__FILE__', __FILE__);
		define('FORMIT_PLUGIN_BASE', plugin_basename(FORMIT__FILE__));
		define('FORMIT_PATH', plugin_dir_path(FORMIT__FILE__));
		define('FORMIT_ASSETS_PATH', FORMIT_PATH . 'assets/');
		define('FORMIT_MODULES_PATH', FORMIT_PATH . 'modules/');
		define('FORMIT_URL', plugins_url('/', FORMIT__FILE__));
		define('FORMIT_ASSETS_URL', FORMIT_URL . 'assets/');
		define('FORMIT_MODULES_URL', FORMIT_URL . 'modules/');
	}


	/**
	 * Do stuff upon plugin activation
	 *
	 * @return void
	 */
	public function activate(){
		$installer = new Xirosoft\Formit\Installer(); 
		$installer->run();
		
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version()
	{
		if (isset($_GET['activate'])) {
			unset($_GET['activate']);
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'formit'),
			'<strong>' . esc_html__('formit Plugin', 'formit') . '</strong>',
			'<strong>' . esc_html__('PHP', 'formit') . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
	}
	
    
}

// Instantiate Formit_Plugin.
new Formit_Plugin();



?>