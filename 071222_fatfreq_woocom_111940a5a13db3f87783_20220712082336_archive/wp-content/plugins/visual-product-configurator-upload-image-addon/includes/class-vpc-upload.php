<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       www.orionorigin.com
 * @since      1.0.0
 *
 * @package    Vpc_Upload
 * @subpackage Vpc_Upload/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Vpc_Upload
 * @subpackage Vpc_Upload/includes
 * @author     ORION <support@orionorigin.com>
 */
class Vpc_Upload {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Vpc_Upload_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'vpc-upload';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Vpc_Upload_Loader. Orchestrates the hooks of the plugin.
	 * - Vpc_Upload_I18n. Defines internationalization functionality.
	 * - Vpc_Upload_Admin. Defines all hooks for the admin area.
	 * - Vpc_Upload_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		* The class responsible for orchestrating the actions and filters of the
		* core plugin.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vpc-upload-loader.php';

		/**
		* The class responsible for defining internationalization functionality
		* of the plugin.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vpc-upload-i18n.php';

		/**
		* The class responsible for defining all actions that occur in the admin area.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vpc-upload-admin.php';

		/**
		* The class responsible for defining all actions that occur in the public-facing
		* side of the site.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-vpc-upload-public.php';

		$this->loader = new Vpc_Upload_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Vpc_Upload_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Vpc_Upload_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Vpc_Upload_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_new_submenu' );
		$this->loader->add_filter( 'vpc_configuration_behaviours', $plugin_admin, 'add_upload_behaviour' );
		$this->loader->add_filter( 'vpc_components_options_fields', $plugin_admin, 'add_vpc_upload_options', 10, 2 );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'run_db_updates_requirements' );
		$this->loader->add_action( 'wp_ajax_run_upload_updater', $plugin_admin, 'run_updater' );
                $this->loader->add_action( 'init', $plugin_admin, 'get_updater');
                $this->loader->add_filter( "vpc_global_settings",$plugin_admin, 'add_global_settings');

		$text_component = new Vpc_Upload_Component( false );
		$this->loader->add_action( 'init', $text_component, 'register_cpt_config' );
		$this->loader->add_action( 'add_meta_boxes', $text_component, 'get_config_metabox' );
		$this->loader->add_action( 'save_post_vpc-upload-component', $text_component, 'save_product_upload_component' );
		$this->loader->add_action( 'vpc_after_preview', $text_component, 'get_js_variables' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Vpc_Upload_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'vpc_enqueue_core_styles', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'vpc_enqueue_core_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'vpc_upload', $plugin_public, 'set_upload_options', 10, 8 );
		// $this->loader->add_filter( 'vpc_preview_container', $plugin_public, 'add_file_on_preview', 10, 3 );
		$this->loader->add_filter( 'vpc_data', $plugin_public, 'add_new_data' );
		$this->loader->add_filter( 'vpc_filter_recap', $plugin_public, 'filter_recap', 10, 3 );
		$this->loader->add_filter( 'vpc_config_price', $plugin_public, 'add_file_price', 10, 4 );
		$this->loader->add_action( 'wp_ajax_vpc_upload_save_image', $plugin_public, 'vpc_upload_save_image' );
		$this->loader->add_action( 'wp_ajax_nopriv_vpc_upload_save_image', $plugin_public, 'vpc_upload_save_image' );
		$this->loader->add_filter( 'get_formatted_config_data', $plugin_public, 'get_new_formatted_config_data', 10, 4 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Vpc_Upload_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
