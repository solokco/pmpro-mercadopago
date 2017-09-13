<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.estoesweb.com
 * @since      1.0.0
 *
 * @package    Pmpro_Mercadopago
 * @subpackage Pmpro_Mercadopago/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Pmpro_Mercadopago
 * @subpackage Pmpro_Mercadopago/includes
 * @author     Carlos Carmona <ccarmona@estoesweb.com>
 */
class Pmpro_Mercadopago_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'pmpro-mercadopago',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
