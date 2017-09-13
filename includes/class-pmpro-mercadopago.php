<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.estoesweb.com
 * @since      1.0.0
 *
 * @package    Pmpro_Mercadopago
 * @subpackage Pmpro_Mercadopago/includes
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
 * @package    Pmpro_Mercadopago
 * @subpackage Pmpro_Mercadopago/includes
 * @author     Carlos Carmona <ccarmona@estoesweb.com>
 */
class Pmpro_Mercadopago {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Pmpro_Mercadopago_Loader    $loader    Maintains and registers all hooks for the plugin.
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

		$this->plugin_name = 'pmpro-mercadopago';
		$this->version = '1.0.0';

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
	 * - Pmpro_Mercadopago_Loader. Orchestrates the hooks of the plugin.
	 * - Pmpro_Mercadopago_i18n. Defines internationalization functionality.
	 * - Pmpro_Mercadopago_Admin. Defines all hooks for the admin area.
	 * - Pmpro_Mercadopago_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pmpro-mercadopago-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pmpro-mercadopago-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pmpro-mercadopago-admin.php';

		if ( function_exists( 'pmpro_gateways' ) )
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pmpro-mercadopago-options.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pmpro-mercadopago-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/lib/mercadopago.php';
		
		$this->loader = new Pmpro_Mercadopago_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Pmpro_Mercadopago_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Pmpro_Mercadopago_i18n();

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

		$plugin_admin = new Pmpro_Mercadopago_Admin( $this->get_plugin_name(), $this->get_version() );
		
		if ( function_exists( 'pmpro_gateways' ) ) :
			
			$this->loader->add_filter('pmpro_gateways'			, $plugin_admin 	, 'pmpro_gateways' );
			$this->loader->add_filter('pmpro_valid_gateways'	, $plugin_admin 	, "add_mercadopago_to_valid_gateways");
			$this->loader->add_filter('pmpro_currencies'		, $plugin_admin 	, 'mercadopago_currencies');
			
			
			$plugin_settings = new PMProGateway_mercadopago();
			
			//add fields to payment settings
			$this->loader->add_filter('pmpro_payment_options', $plugin_settings , 'pmpro_payment_options' );
			$this->loader->add_filter('pmpro_payment_option_fields', $plugin_settings , 'pmpro_payment_option_fields' , 10, 2);
			
			
			
			//add some fields to edit user page (Updates)
			//$this->loader->add_action('pmpro_after_membership_level_profile_fields', $plugin_settings , 'user_profile_fields' );
			//$this->loader->add_action('profile_update', $plugin_settings , 'user_profile_fields_save' );

			//updates cron
			$this->loader->add_action('pmpro_activation', $plugin_settings , 'pmpro_activation' );
			$this->loader->add_action('pmpro_deactivation', $plugin_settings , 'pmpro_deactivation' );
			$this->loader->add_action('pmpro_cron_mercadopago_subscription_updates', $plugin_settings , 'pmpro_cron_mercadopago_subscription_updates' );
			
			$this->loader->add_action('init', $plugin_settings , 'init' );
			
			$this->loader->add_action("pmpro_checkout_boxes", $plugin_settings , "pmproappe_pmpro_checkout_boxes", 20);
			
			
			
		endif;	
		
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'check_pmpro_is_active' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Pmpro_Mercadopago_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

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
	 * @return    Pmpro_Mercadopago_Loader    Orchestrates the hooks of the plugin.
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
