<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://synavos.com
 * @since             1.0.0
 * @package           Spreado
 *
 * @wordpress-plugin
 * Plugin Name:       Spreado
 * Plugin URI:        https://spreado.co
 * Description:       Spreado is a dynamic plugin designed to enhance user engagement and reward loyalty. Upon registering, users can interact with various products through the platform. Each interaction, whether it be like, comment, share, or buy products, earns the user points or rewards.
 * Version:           1.0.0
 * Author:            Synavos
 * Author URI:        https://synavos.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       spreado
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
define( 'SPREADO_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-spreado-activator.php
 */
function activate_spreado() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-spreado-activator.php';
	Spreado_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-spreado-deactivator.php
 */
function deactivate_spreado() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-spreado-deactivator.php';
	Spreado_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_spreado' );
register_deactivation_hook( __FILE__, 'deactivate_spreado' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-spreado.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_spreado() {

	$plugin = new Spreado();
	$plugin->run();

}
run_spreado();
