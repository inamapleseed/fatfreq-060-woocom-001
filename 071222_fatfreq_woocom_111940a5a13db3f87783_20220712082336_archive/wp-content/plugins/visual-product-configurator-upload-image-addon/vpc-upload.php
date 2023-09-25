<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://configuratorsuiteforwp.com/
 * @since             1.0.0
 * @package           Vpc_Upload
 *
 * @wordpress-plugin
 * Plugin Name:       Visual Products Configurator Upload Image Addon
 * Plugin URI:        https://demos.configuratorsuiteforwp.com/
 * Description:       This plugin allows you to upload your own images and display it on configuration preview.
 * Version:           2.11
 * Author:            ORION
 * Author URI:        https://configuratorsuiteforwp.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       vpc-upload
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'VPC_UPLOAD_VERSION', '2.11' );
define( 'VPC_UPLOAD_MAIN_FILE', 'visual-product-configurator-upload-image-addon/vpc-upload.php' );
define( 'VPC_UPLOAD_NAME', 'Visual Product Configurator Upload Image Add-on' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-vpc-upload-activator.php
 */
function activate_vpc_upload() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vpc-upload-activator.php';
	Vpc_Upload_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-vpc-upload-deactivator.php
 */
function deactivate_vpc_upload() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vpc-upload-deactivator.php';
	Vpc_Upload_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_vpc_upload' );
register_deactivation_hook( __FILE__, 'deactivate_vpc_upload' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-vpc-upload.php';
require plugin_dir_path( __FILE__ ) . 'includes/functions.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-vpc-upload-component.php';
define( 'VPC_UPLOAD_URL', plugins_url( '/', __FILE__ ) );
define( 'VPC_UPLOAD_DIR', dirname( __FILE__ ) );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_vpc_upload() {

	$plugin = new Vpc_Upload();
	$plugin->run();

}
run_vpc_upload();
