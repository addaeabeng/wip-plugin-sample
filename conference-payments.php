<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://addaeabeng.co.uk
 * @since             1.0.0
 * @package           Conference_Payments
 *
 * @wordpress-plugin
 * Plugin Name:       Conference Payments
 * Plugin URI:        https://addaeabeng.co.uk
 * Description:       Adds the ability to purchase tickets for conferences via external site
 * Version:           1.0.0
 * Author:            Addae
 * Author URI:        https://addaeabeng.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       conference-payments
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
define( 'CONFERENCE_PAYMENTS_VERSION', '1.0.0' );
define( 'CPAAPREFIX', '_cpaa_');
// External API KEY HERE
define( 'API_KEY', '');


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-conference-payments-activator.php
 */
function activate_conference_payments() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-conference-payments-activator.php';
	Conference_Payments_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-conference-payments-deactivator.php
 */
function deactivate_conference_payments() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-conference-payments-deactivator.php';
	Conference_Payments_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_conference_payments' );
register_deactivation_hook( __FILE__, 'deactivate_conference_payments' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-conference-payments.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_conference_payments() {

	$plugin = new AA_ConfPayments\Conference_Payments();
	$plugin->run();

}
run_conference_payments();
