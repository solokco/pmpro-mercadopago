<?php
//load classes init method
//add_action('init', array('PMProGateway_mercadopago', 'init'));

/**
 * PMProGateway_gatewayname Class
 *
 * Handles mercadopago integration.
 *
 */
	
class PMProGateway_mercadopago extends PMProGateway {
		
	private $access_token; 
	
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name = 'pmpro-mercadopago';


	public function __construct( $gateway = NULL ) {
		
		$this->gateway = $gateway;
		
	}
										
	/**
	 * Run on WP init
	 *
	 * @since 1.8
	 */
	static function init() {
		
		//code to add at checkout if mercadopago is the current gateway
		$gateway = pmpro_getOption("gateway");

		if ( $gateway == "mercadopago") {
			//add_action('pmpro_checkout_preheader', array('PMProGateway_mercadopago', 'pmpro_checkout_preheader'));
			//add_filter('pmpro_checkout_order', array('PMProGateway_mercadopago', 'pmpro_checkout_order'));
			//add_filter('pmpro_include_billing_address_fields', array('PMProGateway_mercadopago', 'pmpro_include_billing_address_fields'));
			//add_filter('pmpro_include_cardtype_field', array('PMProGateway_mercadopago', 'pmpro_include_billing_address_fields'));
			//add_filter('pmpro_include_payment_information_fields', array('PMProGateway_mercadopago', 'pmpro_include_payment_information_fields'));
			
			add_filter('pmpro_include_billing_address_fields', '__return_false');
			add_filter('pmpro_include_payment_information_fields', '__return_false');
			
			add_filter('pmpro_required_billing_fields', array('PMProGateway_mercadopago', 'pmpro_required_billing_fields'));
			add_filter('pmpro_checkout_new_user_array', array('PMProGateway_mercadopago', 'pmpro_checkout_new_user_array'));
			
			add_filter('pmpro_checkout_confirmed', array('PMProGateway_mercadopago', 'pmpro_checkout_confirmed'));
			add_action('pmpro_checkout_before_processing', array('PMProGateway_mercadopago', 'pmpro_checkout_before_processing'));
			
			add_filter('pmpro_checkout_default_submit_button', array('PMProGateway_mercadopago', 'pmpro_checkout_default_submit_button'));
			add_filter('pmpro_checkout_before_change_membership_level', array('PMProGateway_mercadopago', 'pmpro_checkout_before_change_membership_level'), 10, 2);
			
			//add_action('pmpro_checkout_after_form', array('PMProGateway_mercadopago', 'pmpro_checkout_after_form'));
			//add_action('http_api_curl', array('PMProGateway_mercadopago', 'http_api_curl'), 10, 3);
			
		}	
		
	}

	/**
	 * Get a list of payment options that the mercadopago gateway needs/supports.
	 *
	 * @since 1.8
	 */
	static function getGatewayOptions() {
		
		$options = array(
			'sslseal',
			'nuclear_HTTPS',
			'gateway_environment',
			'mercadopago_email',
            'mercadopago_publickey',  
            'mercadopago_privatekey',  
			'currency',
			'use_ssl',
			'pais_mercadopago',
			'mercadopago_skip_confirmation'
		);

		return $options;
	}

	/**
	 * Set payment options for payment settings page.
	 *
	 * @since 1.8
	 */
	static function pmpro_payment_options($options) {
		
		//get mercadopago options
		$mercadopago_options = PMProGateway_mercadopago::getGatewayOptions();

		//merge with others.
		$options = array_merge($mercadopago_options, $options);

		return $options;
	}

	/**
	 * Display fields for mercadopago options.
	 *
	 * @since 1.8
	 */
	static function pmpro_payment_option_fields($values, $gateway) { 	
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/pmpro-mercadopago-admin-settings.php';

		
	}
	
	/**
	 * Remove required billing fields
	 *
	 * @since 1.8
	 */
	static function pmpro_required_billing_fields($fields) {
		unset($fields['bfirstname']);
		unset($fields['blastname']);
		unset($fields['baddress1']);
		unset($fields['bcity']);
		unset($fields['bstate']);
		unset($fields['bzipcode']);
		unset($fields['bphone']);
		unset($fields['bemail']);
		unset($fields['bcountry']);
		unset($fields['CardType']);
		unset($fields['AccountNumber']);
		unset($fields['ExpirationMonth']);
		unset($fields['ExpirationYear']);
		unset($fields['CVV']);

		return $fields;
	}
	
	
	static function pmpro_checkout_before_processing() {
		global $current_user, $gateway;

		//save user fields for Mercado Pago
		if(!$current_user->ID)
		{
			//get values from post
			if(isset($_REQUEST['username']))
				$username = trim(sanitize_text_field($_REQUEST['username']));
			else
				$username = "";
			if(isset($_REQUEST['password']))
				$password = sanitize_text_field($_REQUEST['password']);
			else
				$password = "";
			if(isset($_REQUEST['bemail']))
				$bemail = sanitize_email($_REQUEST['bemail']);
			else
				$bemail = "";

			//save to session
			$_SESSION['pmpro_signup_username'] = $username;
			$_SESSION['pmpro_signup_password'] = $password;
			$_SESSION['pmpro_signup_email'] = $bemail;
		}

		//can use this hook to save some other variables to the session
		do_action("pmpro_paypalexpress_session_vars");
	}

	
	/**
	 * Swap in user/pass/etc from session
	 *
	 * @since 1.8
	 */
	static function pmpro_checkout_new_user_array($new_user_array) {
		global $current_user;

		if(!$current_user->ID)
		{
			//reload the user fields
			$new_user_array['user_login'] = $_SESSION['pmpro_signup_username'];
			$new_user_array['user_pass'] = $_SESSION['pmpro_signup_password'];
			$new_user_array['user_email'] = $_SESSION['pmpro_signup_email'];

			//unset the user fields in session
			unset($_SESSION['pmpro_signup_username']);
			unset($_SESSION['pmpro_signup_password']);
			unset($_SESSION['pmpro_signup_email']);
		}

		return $new_user_array;
	}
	
	

	/**
	 * Swap in our submit buttons.
	 *
	 * @since 1.8
	 */
	
	static function pmpro_checkout_default_submit_button($show) {
		
		global $gateway, $pmpro_requirebilling, $gateway_environment;
		
		//show our submit buttons
		?>

		<span id="pmpro_submit_span" <?php if( $gateway == "mercadopago" && $pmpro_requirebilling ) ?> >
			<input type="hidden" name="submit-checkout" value="1" />
			<input type="submit" class="pmpro_btn pmpro_btn-submit-checkout" value="<?php if($pmpro_requirebilling) { _e('Pagar con Mercadopago', 'paid-memberships-pro' ); } else { _e('Pagar con Mercadopago', 'paid-memberships-pro' );}?> &raquo;" />
		</span>
		
		<?php

		//don't show the default
		return false;
	}
	
	//Mercado Pago, this is run first to authorize from Mercadopago
	function setExpressCheckout(&$order) {
		
		global $pmpro_currency , $gateway_environment;
		
		$environment = $gateway_environment;
		
		if(empty($order->code))
			$order->code = $order->getRandomCode();
			
			
		$mp = new MP ( pmpro_getOption('mercadopago_publickey') , pmpro_getOption('mercadopago_privatekey') );
		$access_token = $mp->get_access_token();
		
		//print_r($order);
		
		$moneda = pmpro_getOption("currency");
		
		if("sandbox" === $environment || "beta-sandbox" === $environment) {
			
			$mp->sandbox_mode(TRUE);
		
		} else {
			
			$mp->sandbox_mode(FALSE);
			
		}
		
		$preference_data = array (
		    "id"	=> $order->code ,
		    
		    "items" => array (
		        array (
		            "id"			=> $order->code,
		            "title" 		=> $order->membership_level->name ,
		            "quantity" 		=> 1,
		            "currency_id" 	=> $moneda,
		            "unit_price" 	=> (float) $order->InitialPayment,
		            "description"	=> $order->membership_level->name . " at " . get_bloginfo("name") ,
		            "category_id"	=> "virtual_goods"
		        )
		    ),
		    
		    "payer" => array( 
				"email"			=> $order->Email
				//"date_created"	=> "2015-06-02T12:58:41.425-04:00"				
			),
		    
		    
		    "back_urls" => array (
				"success" =>  pmpro_url( "checkout", "?level=" . $order->membership_level->id ) ,
				"failure" =>  pmpro_url( "checkout", "?level=" . $order->membership_level->id ) ,
				"pending" =>  pmpro_url( "checkout", "?level=" . $order->membership_level->id ) 
			),
			
			"notification_url"	=> "",
			"auto_return" 		=> "approved",
			//"expires" => true,
			//"expiration_date_from" => $order->ProfileStartDate,
			//"expiration_date_to" => $order->ExpirationDate,
			"external_reference" => $order->code

		);
		
		$preference = $mp->create_preference($preference_data);
		
		$order->saveOrder();
		
		if("sandbox" === $environment || "beta-sandbox" === $environment) {
			
			wp_redirect($preference['response']['sandbox_init_point'] );
		
		} else {
			
			wp_redirect($preference['response']['init_point'] );
			
		}
		//write session?

		//redirect to Mercadopago
	}

	function getExpressCheckoutDetails(&$order) {
		$nvpStr="&TOKEN=".$order->Token;

		$nvpStr = apply_filters("pmpro_get_express_checkout_details_nvpstr", $nvpStr, $order);
		
		//print_r($order);
		
		/* Make the API call and store the results in an array.  If the
		call was a success, show the authorization details, and provide
		an action to complete the payment.  If failed, show the error
		*/
		$this->httpParsedResponseAr = $this->PPHttpPost('GetExpressCheckoutDetails', $nvpStr);

		if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"])) {
			$order->status = "review";

			//update order
			$order->saveOrder();

			return true;
		} else  {
			$order->status = "error";
			$order->errorcode = $this->httpParsedResponseAr['L_ERRORCODE0'];
			$order->error = urldecode($this->httpParsedResponseAr['L_LONGMESSAGE0']);
			$order->shorterror = urldecode($this->httpParsedResponseAr['L_SHORTMESSAGE0']);
			return false;
			//exit('SetExpressCheckout failed: ' . print_r($httpParsedResponseAr, true));
		}
	}

	/**
	 * Process at checkout
	 *
	 * Repurposed in v2.0. The old process() method is now confirm().
	 */

	function process(&$order) {

		$order->payment_type = "Mercadopago";
		$order->cardtype = "";
		$order->ProfileStartDate = date_i18n("Y-m-d", strtotime("+ " . $order->BillingFrequency . " " . $order->BillingPeriod)) . "T0:0:0";
		$order->ProfileStartDate = apply_filters("pmpro_profile_start_date", $order->ProfileStartDate, $order);
		
		return $this->setExpressCheckout($order);
	}

	/**
	 * Process charge or subscription after confirmation.
	 *
	 * @since 1.8
	 */
	function confirm(&$order) {
		
		if(pmpro_isLevelRecurring($order->membership_level)) {
			$order->ProfileStartDate = date_i18n("Y-m-d", strtotime("+ " . $order->BillingFrequency . " " . $order->BillingPeriod, current_time("timestamp"))) . "T0:0:0";
			$order->ProfileStartDate = apply_filters("pmpro_profile_start_date", $order->ProfileStartDate, $order);
			return $this->subscribe($order);
		
		}
		
		else
			return $this->charge($order);
	}

	static function pmpro_checkout_confirmed($pmpro_confirmed) {
		
		global $pmpro_msg, $pmpro_msgt, $pmpro_level, $current_user, $pmpro_review, $pmpro_paypal_token, $discount_code, $bemail;
		
		//print_r($_REQUEST);
		
		if( empty( $pmpro_msg ) && ( !empty( $_REQUEST['collection_status'] ) ) ) {
			
			$morder = new MemberOrder();
			
			//$morder->getMemberOrderByPayPalToken(sanitize_text_field($_REQUEST['token']));
			$morder->Token = $_REQUEST['collection_id']; 
			
			//$pmpro_paypal_token = $morder->paypal_token;
			
			if ( $_REQUEST['collection_status'] == "approved" ) {
			
				//set up values
				$morder->membership_id 		= $pmpro_level->id;
				$morder->membership_name 	= $pmpro_level->name;
				$morder->discount_code 		= $discount_code;
				$morder->InitialPayment 	= $pmpro_level->initial_payment;
				$morder->PaymentAmount 		= $pmpro_level->billing_amount;
				$morder->ProfileStartDate 	= date_i18n("Y-m-d") . "T0:0:0";
				$morder->BillingPeriod 		= $pmpro_level->cycle_period;
				$morder->BillingFrequency 	= $pmpro_level->cycle_number;
				$morder->Email 				= $bemail;

				//set up level var
				$morder->getMembershipLevel();
				$morder->membership_level = apply_filters("pmpro_checkout_level", $morder->membership_level);

				//tax
				$morder->subtotal = $morder->InitialPayment;
				$morder->getTax();
				
				if($pmpro_level->billing_limit)
					$morder->TotalBillingCycles = $pmpro_level->billing_limit;

				if(pmpro_isLevelTrial($pmpro_level)) {
					$morder->TrialBillingPeriod = $pmpro_level->cycle_period;
					$morder->TrialBillingFrequency = $pmpro_level->cycle_number;
					$morder->TrialBillingCycles = $pmpro_level->trial_limit;
					$morder->TrialAmount = $pmpro_level->trial_amount;
				}

				if($morder->confirm()) {
					$pmpro_confirmed = true;
				}
				
				else {
					$pmpro_msg = $morder->error;
					$pmpro_msgt = "pmpro_error";
				}
			}
			
			else {
				$pmpro_msg = __("La orden no fue aprobada.", 'paid-memberships-pro' );
				$pmpro_msgt = "pmpro_error";
			}
		}
		
		if(!empty($morder))
			return array("pmpro_confirmed"=>$pmpro_confirmed, "morder"=>$morder);
		else
			return $pmpro_confirmed;
	
	}
	
	
	/*
		Check if a PayPal gateway is enabled for PMPro.
	*/
	function pmproappe_using_paypal( $check_gateway = null ) {
	
		if (is_null($check_gateway)) {
	
			global $gateway;
			$check_gateway = $gateway;
		}
	
		$paypal_gateways = apply_filters('pmpro_paypal_gateways', array('paypal', 'paypalstandard', 'paypalexpress' ) );
	
		if ( in_array($check_gateway, $paypal_gateways)) {
			return true;
		}
	
		return false;
	}

	// AGREGO LAS OPCIONES DE PAGO
	function pmproappe_pmpro_checkout_boxes() {
		
	}
	
			
}	