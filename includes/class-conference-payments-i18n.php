<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://addaeabeng.co.uk
 * @since      1.0.0
 *
 * @package    Conference_Payments
 * @subpackage Conference_Payments/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Conference_Payments
 * @subpackage Conference_Payments/includes
 * @author     Addae <Abeng>
 */

namespace AA_ConfPayments;

class Conference_Payments_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'conference-payments',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
