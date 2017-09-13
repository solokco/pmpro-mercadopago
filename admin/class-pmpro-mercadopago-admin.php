<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.estoesweb.com
 * @since      1.0.0
 *
 * @package    Pmpro_Mercadopago
 * @subpackage Pmpro_Mercadopago/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pmpro_Mercadopago
 * @subpackage Pmpro_Mercadopago/admin
 * @author     Carlos Carmona <ccarmona@estoesweb.com>
 */
class Pmpro_Mercadopago_Admin extends PMProGateway {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pmpro_Mercadopago_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pmpro_Mercadopago_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pmpro-mercadopago-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pmpro_Mercadopago_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pmpro_Mercadopago_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pmpro-mercadopago-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . "-options", plugin_dir_url( __FILE__ ) . 'js/pmpro-mercadopago-options.js', array( 'jquery' ), $this->version, true );

	}
	
	public function check_pmpro_is_active() {
		
		if(  ! is_plugin_active( 'paid-memberships-pro/paid-memberships-pro.php' ) ) {
			
			$class = 'notice notice-error';
			$message = __( 'Irks! El plugin de Mercadopago para PMPRO requiere que esté activado el plugin Paid Memberships Pro.', 'pmpro-mercadopago' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			
			return false;	
		}
		
		return true;
			
	}
	
	// AGREGAR NUEVO GATEWAY
	public function add_mercadopago_to_valid_gateways($gateways) {
	    $gateways[] = "mercadopago";
	    return $gateways;
	}
	
	public function mercadopago_currencies ($currencies) {
		
		$currencies['VEF'] = array(
								"name" 		=> __('Bolivares Fuertes' , $this->plugin_name ),
								'symbol' 	=> 'Bsf ',
								'position' => 'left',
								'decimals' => 2
							);
		
		$currencies['CLP'] = array(
								"name" 		=> __('Peso Chileno' , $this->plugin_name ),
								'symbol' 	=> '$ ',
								'position' => 'left',
								'decimals' => 2
							);
		
		
		
		$currencies['PEN'] = array(
								"name" 		=> __('Nuevo Sol de Peru' , $this->plugin_name ),
								'symbol' 	=> 'S/. ',
								'position' => 'left',
								'decimals' => 2
							);
		
		
		$currencies['UYU'] = array(
								"name" 		=> __('Peso Uruguayo' , $this->plugin_name ),
								'symbol' 	=> '$ ',
								'position' => 'left',
								'decimals' => 2
							);
		
		
		
		//sort($currencies);
		
		return $currencies;
	}
	
	/**
	 * Make sure mercadopago is in the gateways list
	 *
	 * @since 1.8
	 */
	static function pmpro_gateways($gateways) {

		if(empty($gateways['mercadopago']))
			$gateways['mercadopago'] = __('Mercadopago', 'pmpro');

		return $gateways;
	}

}