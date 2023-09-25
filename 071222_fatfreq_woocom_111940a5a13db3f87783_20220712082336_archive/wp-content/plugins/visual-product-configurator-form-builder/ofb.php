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
 * @package           Ofb
 *
 * @wordpress-plugin
 * Plugin Name:       Visual Products Configurator Form Builder Addon
 * Plugin URI:        https://demos.configuratorsuiteforwp.com/configuration-with-form/
 * Description:       A form builder designed to work as add-on for ORION extensions only, not as an independant form builder plugin.
 * Version:           1.8
 * Author:            ORION
 * Author URI:        https://configuratorsuiteforwp.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ofb
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('VPC_OFB_VERSION', '1.8' );
define('VPC_OFB_MAIN_FILE', 'visual-product-configurator-form-builder/ofb.php' );
define('VPC_OFB_NAME', 'Visual Product Configurator Form Builder Add-On');
define('VPC_OFB_URL', plugins_url( '/', __FILE__ ) );
define('VPC_OFB_DIR', dirname( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ofb-activator.php
 */
function activate_ofb() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ofb-activator.php';
	Ofb_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ofb-deactivator.php
 */
function deactivate_ofb() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ofb-deactivator.php';
	Ofb_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ofb' );
register_deactivation_hook( __FILE__, 'deactivate_ofb' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ofb.php';
require plugin_dir_path( __FILE__ ) . 'includes/functions.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ofb() {

	$plugin = new Ofb();
	$plugin->run();

}
run_ofb();
