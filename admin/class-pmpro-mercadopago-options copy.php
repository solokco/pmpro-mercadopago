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
	
	function __construct($gateway = NULL)  {

		$this->gateway = $gateway;
		return $this->gateway;
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
			add_filter('pmpro_checkout_order', array('PMProGateway_mercadopago', 'pmpro_checkout_order'));
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
			add_action('pmpro_checkout_after_form', array('PMProGateway_mercadopago', 'pmpro_checkout_after_form'));
			add_action('http_api_curl', array('PMProGateway_mercadopago', 'http_api_curl'), 10, 3);
			
		}
		
		
		
		//$mp = new MP ("TEST-7440435519564174-082422-721db547afe42c4b83838f9c12e6dcc1__LD_LB__-212362207");
		
/*
		$preference_data = array (
		    "items" => array (
		        array (
		            "title" => "Test",
		            "quantity" => 1,
		            "currency_id" => "VEF",
		            "unit_price" => 10.4
		        )
		    )
		);
		
		$preference = $mp->create_preference($preference_data);
		
		print_r ($preference);
*/
		
		
		
	}
	
	
	/**
	 * Update the SSLVERSION for CURL to support PayPal Express moving to TLS 1.2
	 *
	 * @since 1.8.9.1
	 */
	static function http_api_curl($handle, $r, $url) {
		if(strpos($url, 'paypal.com') !== false)
			curl_setopt( $handle, CURLOPT_SSLVERSION, 6 );
	}
	
	
	/**
	 * Make sure mercadopago is in the gateways list
	 *
	 * @since 1.8
	 */
	static function pmpro_gateways($gateways)
	{
		if(empty($gateways['mercadopago']))
			$gateways['mercadopago'] = __('Mercadopago', 'pmpro');

		return $gateways;
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
			'gateway_environment',
			'gateway_email',
            'mercadopago_publickey',  
            'mercadopago_privatekey',  
			'currency',
			'use_ssl',
			'tax_state',
			'tax_rate',
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
	static function pmpro_payment_option_fields($values, $gateway) { ?>
		<tr class="pmpro_settings_divider gateway gateway_mercadopago" <?php if($gateway != "mercadopago") { ?>style="display: none;"<?php } ?>>
			<td colspan="2">
				<?php _e('Mercadopago Settings', 'pmpro'); ?>
			</td>
		</tr>
		
		<tr class="gateway gateway_mercadopago" <?php if($gateway != "mercadopago") { ?>style="display: none;"<?php } ?>>

			<th scope="row" valign="top"> 
	            <label for="gateway_email"><?php _e('Email', 'paid-memberships-pro' );?>:</label> 
	        </th> 
	        
	        <td> 
	            <input type="text" id="gateway_email" name="gateway_email" size="60" value="<?php echo esc_attr($values['gateway_email'])?>" /> 
	            <br /><small><?php _e('El email del usuario con el que ingresas a Mercado Pago');?></small> 
	        </td> 
			
		</tr>
		
		<tr class="gateway gateway_mercadopago" <?php if($gateway != "mercadopago") { ?>style="display: none;"<?php } ?>>

			<th scope="row" valign="top"> 
	            <label for="mercadopago_publickey"><?php _e('Public Key', 'paid-memberships-pro' );?>:</label> 
	        </th> 
	        
	        <td> 
	            <input type="text" id="mercadopago_publickey" name="mercadopago_publickey" size="60" value="<?php echo esc_attr($values['mercadopago_publickey'])?>" /> 
	        </td> 
			
		</tr>
		
		<tr class="gateway gateway_mercadopago" <?php if($gateway != "mercadopago") { ?>style="display: none;"<?php } ?>>

			<th scope="row" valign="top"> 
	            <label for="mercadopago_privatekey"><?php _e('Private Key', 'paid-memberships-pro' );?>:</label> 
	        </th> 
	        
	        <td> 
	            <input type="text" id="mercadopago_privatekey" name="mercadopago_privatekey" size="60" value="<?php echo esc_attr($values['mercadopago_privatekey'])?>" /> 
	        </td> 
			
		</tr>
		<?php
	}
	
	/**
	 * Remove required billing fields
	 *
	 * @since 1.8
	 */
	static function pmpro_required_billing_fields($fields)
	{
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
	
	
	static function pmpro_checkout_before_processing()
	{
		global $current_user, $gateway;

		//save user fields for PayPal Express
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
	static function pmpro_checkout_new_user_array($new_user_array)
	{
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
	
	static function pmpro_checkout_order($morder) {
		
		$mp = new MP ( pmpro_getOption('mercadopago_publickey') , pmpro_getOption('mercadopago_privatekey') );
		$access_token = $mp->get_access_token();

		print_r($morder);
		
		echo "El acces Token es " . $access_token;
		
		return $morder;
		
	}
	
	/**
	 * Process at checkout
	 *
	 * Repurposed in v2.0. The old process() method is now confirm().
	 */

/*
	function process(&$order) {
		$order->payment_type = "Mercadopago";
		$order->cardtype = "";
		$order->ProfileStartDate = date_i18n("Y-m-d", strtotime("+ " . $order->BillingFrequency . " " . $order->BillingPeriod)) . "T0:0:0";
		$order->ProfileStartDate = apply_filters("pmpro_profile_start_date", $order->ProfileStartDate, $order);

		
		return $this->setExpressCheckout($order);
	}
*/

	/**
	 * Process charge or subscription after confirmation.
	 *
	 * @since 1.8
	 */
/*
	function confirm(&$order)
	{
		if(pmpro_isLevelRecurring($order->membership_level))
		{
			$order->ProfileStartDate = date_i18n("Y-m-d", strtotime("+ " . $order->BillingFrequency . " " . $order->BillingPeriod, current_time("timestamp"))) . "T0:0:0";
			$order->ProfileStartDate = apply_filters("pmpro_profile_start_date", $order->ProfileStartDate, $order);
			return $this->subscribe($order);
		}
		else
			return $this->charge($order);
	}
*/

	/**
	 * Swap in our submit buttons.
	 *
	 * @since 1.8
	 */
	static function pmpro_checkout_default_submit_button($show)
	{
		global $gateway, $pmpro_requirebilling;

		//show our submit buttons
		?>
		<span id="pmpro_paypalexpress_checkout" <?php if(($gateway != "paypalexpress" && $gateway != "paypalstandard") || !$pmpro_requirebilling) { ?>style="display: none;"<?php } ?>>
			<input type="hidden" name="submit-checkout" value="1" />
			<input type="image" class="pmpro_btn-submit-checkout" value="<?php _e('Check Out with PayPal', 'paid-memberships-pro' );?> &raquo;" src="<?php echo apply_filters("pmpro_paypal_button_image", "https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif");?>" />
		</span>
		
		<span id="pmpro_submit_span" <?php if(($gateway == "paypalexpress" || $gateway == "paypalstandard") && $pmpro_requirebilling) { ?>style="display: none;"<?php } ?>>
			<input type="hidden" name="submit-checkout" value="1" />
			<input type="submit" class="pmpro_btn pmpro_btn-submit-checkout" value="<?php if($pmpro_requirebilling) { _e('Submit and Check Out', 'paid-memberships-pro' ); } else { _e('Submit and Confirm', 'paid-memberships-pro' );}?> &raquo;" />
		</span>
		<?php

		//don't show the default
		return false;
	}

	/**
	 * Scripts for checkout page.
	 *
	 * @since 1.8
	 */
	static function pmpro_checkout_after_form()
	{
	?>
	<script>
		<!--
		//choosing payment method
		jQuery('input[name=gateway]').click(function() {
			if(jQuery(this).val() == 'paypal')
			{
				jQuery('#pmpro_paypalexpress_checkout').hide();
				jQuery('#pmpro_billing_address_fields').show();
				jQuery('#pmpro_payment_information_fields').show();
				jQuery('#pmpro_submit_span').show();
			}
			else
			{
				jQuery('#pmpro_billing_address_fields').hide();
				jQuery('#pmpro_payment_information_fields').hide();
				jQuery('#pmpro_submit_span').hide();
				jQuery('#pmpro_paypalexpress_checkout').show();
			}
		});

		//select the radio button if the label is clicked on
		jQuery('a.pmpro_radio').click(function() {
			jQuery(this).prev().click();
		});
		-->
	</script>
	<?php
	}

	//Mercado Pago, this is run first to authorize from Mercadopago
	function setExpressCheckout(&$order) {
		global $pmpro_currency;
		
		if(empty($order->code))
			$order->code = $order->getRandomCode();
			
			
		$mp = new MP ( pmpro_getOption('mercadopago_publickey') , pmpro_getOption('mercadopago_privatekey') );
		$access_token = $mp->get_access_token();
		
		print_r($order);
		
		//clean up a couple values
		$order->payment_type = "Mercadopago";
		$order->CardType = "";
		$order->cardtype = "";

		//taxes on initial amount
		$initial_payment = $order->InitialPayment;
		$initial_payment_tax = $order->getTaxForPrice($initial_payment);
		$initial_payment = round((float)$initial_payment + (float)$initial_payment_tax, 2);

		//taxes on the amount
		$amount = $order->PaymentAmount;
		$amount_tax = $order->getTaxForPrice($amount);
		$amount = round((float)$amount + (float)$amount_tax, 2);

		//paypal profile stuff
		$nvpStr = "";
		$nvpStr .="&AMT=" . $initial_payment . "&CURRENCYCODE=" . $pmpro_currency;
		
		if(!empty($order->ProfileStartDate) && strtotime($order->ProfileStartDate, current_time("timestamp")) > 0)
			$nvpStr .= "&PROFILESTARTDATE=" . $order->ProfileStartDate;
		
		if(!empty($order->BillingFrequency))
			$nvpStr .= "&BILLINGPERIOD=" . $order->BillingPeriod . "&BILLINGFREQUENCY=" . $order->BillingFrequency . "&AUTOBILLOUTAMT=AddToNextBilling&L_BILLINGTYPE0=RecurringPayments";
		
		$nvpStr .= "&DESC=" . urlencode( apply_filters( 'pmpro_paypal_level_description', substr($order->membership_level->name . " at " . get_bloginfo("name"), 0, 127), $order->membership_level->name, $order, get_bloginfo("name")) );
		$nvpStr .= "&NOTIFYURL=" . urlencode(admin_url('admin-ajax.php') . "?action=ipnhandler");
		$nvpStr .= "&NOSHIPPING=1&L_BILLINGAGREEMENTDESCRIPTION0=" . urlencode(substr($order->membership_level->name . " at " . get_bloginfo("name"), 0, 127)) . "&L_PAYMENTTYPE0=Any";

		//if billing cycles are defined
		if(!empty($order->TotalBillingCycles))
			$nvpStr .= "&TOTALBILLINGCYCLES=" . $order->TotalBillingCycles;

		//if a trial period is defined
		if(!empty($order->TrialBillingPeriod)) {
			$trial_amount = $order->TrialAmount;
			$trial_tax = $order->getTaxForPrice($trial_amount);
			$trial_amount = round((float)$trial_amount + (float)$trial_tax, 2);

			$nvpStr .= "&TRIALBILLINGPERIOD=" . $order->TrialBillingPeriod . "&TRIALBILLINGFREQUENCY=" . $order->TrialBillingFrequency . "&TRIALAMT=" . $trial_amount;
		}

		if(!empty($order->TrialBillingCycles))
			$nvpStr .= "&TRIALTOTALBILLINGCYCLES=" . $order->TrialBillingCycles;

		if(!empty($order->discount_code)) {
			$nvpStr .= "&ReturnUrl=" . urlencode(pmpro_url("checkout", "?level=" . $order->membership_level->id . "&discount_code=" . $order->discount_code . "&review=" . $order->code));
		}
		else{
			$nvpStr .= "&ReturnUrl=" . urlencode(pmpro_url("checkout", "?level=" . $order->membership_level->id . "&review=" . $order->code));
		}

		$additional_parameters = apply_filters("pmpro_paypal_express_return_url_parameters", array());

		if(!empty($additional_parameters)) {
			foreach($additional_parameters as $key => $value)
				$nvpStr .= urlencode("&" . $key . "=" . $value);
		}

		$nvpStr .= "&CANCELURL=" . urlencode(pmpro_url("levels"));

		$account_optional = apply_filters('pmpro_paypal_account_optional', true);

		if ($account_optional)
    		$nvpStr .= '&SOLUTIONTYPE=Sole&LANDINGPAGE=Billing';

		$nvpStr = apply_filters("pmpro_set_express_checkout_nvpstr", $nvpStr, $order);

		///echo str_replace("&", "&<br />", $nvpStr);
		///exit;

		$this->httpParsedResponseAr = $this->PPHttpPost('SetExpressCheckout', $nvpStr);

		if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"])) {
			$order->status = "token";
			$order->paypal_token = urldecode($this->httpParsedResponseAr['TOKEN']);

			//update order
			$order->saveOrder();

			//redirect to paypal
			$paypal_url = "https://www.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=" . $this->httpParsedResponseAr['TOKEN'];
			$environment = pmpro_getOption("gateway_environment");
			if("sandbox" === $environment || "beta-sandbox" === $environment)
			{
				$paypal_url = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token="  . $this->httpParsedResponseAr['TOKEN'];
			}

			wp_redirect($paypal_url);
			exit;

			//exit('SetExpressCheckout Completed Successfully: '.print_r($this->httpParsedResponseAr, true));
		} else  {
			$order->status = "error";
			$order->errorcode = $this->httpParsedResponseAr['L_ERRORCODE0'];
			$order->error = urldecode($this->httpParsedResponseAr['L_LONGMESSAGE0']);
			$order->shorterror = urldecode($this->httpParsedResponseAr['L_SHORTMESSAGE0']);
			return false;
			//exit('SetExpressCheckout failed: ' . print_r($httpParsedResponseAr, true));
		}

		//write session?

		//redirect to PayPal
	}

	function getExpressCheckoutDetails(&$order) {
		$nvpStr="&TOKEN=".$order->Token;

		$nvpStr = apply_filters("pmpro_get_express_checkout_details_nvpstr", $nvpStr, $order);

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

/*
	function charge(&$order)
	{
		global $pmpro_currency;

		if(empty($order->code))
			$order->code = $order->getRandomCode();

		//taxes on the amount
		$amount = $order->InitialPayment;
		$amount_tax = $order->getTaxForPrice($amount);
		$order->subtotal = $amount;
		$amount = round((float)$amount + (float)$amount_tax, 2);

		//paypal profile stuff
		$nvpStr = "";
		if(!empty($order->Token))
			$nvpStr .= "&TOKEN=" . $order->Token;
		$nvpStr .="&AMT=" . $amount . "&CURRENCYCODE=" . $pmpro_currency;
		/*
		if(!empty($amount_tax))
			$nvpStr .= "&TAXAMT=" . $amount_tax;
		*/
		if(!empty($order->BillingFrequency))
			$nvpStr .= "&BILLINGPERIOD=" . $order->BillingPeriod . "&BILLINGFREQUENCY=" . $order->BillingFrequency . "&AUTOBILLOUTAMT=AddToNextBilling";
		$nvpStr .= "&DESC=" . urlencode( apply_filters( 'pmpro_paypal_level_description', substr($order->membership_level->name . " at " . get_bloginfo("name"), 0, 127), $order->membership_level->name, $order, get_bloginfo("name")) );
		$nvpStr .= "&NOTIFYURL=" . urlencode(admin_url('admin-ajax.php') . "?action=ipnhandler");
		$nvpStr .= "&NOSHIPPING=1";

		$nvpStr .= "&PAYERID=" . $_SESSION['payer_id'] . "&PAYMENTACTION=sale";

		$nvpStr = apply_filters("pmpro_do_express_checkout_payment_nvpstr", $nvpStr, $order);

		$order->nvpStr = $nvpStr;

		$this->httpParsedResponseAr = $this->PPHttpPost('DoExpressCheckoutPayment', $nvpStr);

		if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"])) {
			$order->payment_transaction_id = urldecode($this->httpParsedResponseAr['TRANSACTIONID']);
			$order->status = "success";

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
*/

	function subscribe(&$order)
	{
		global $pmpro_currency;

		if(empty($order->code))
			$order->code = $order->getRandomCode();

		//filter order before subscription. use with care.
		$order = apply_filters("pmpro_subscribe_order", $order, $this);

		//taxes on initial amount
		$initial_payment = $order->InitialPayment;
		$initial_payment_tax = $order->getTaxForPrice($initial_payment);
		$initial_payment = round((float)$initial_payment + (float)$initial_payment_tax, 2);

		//taxes on the amount
		$amount = $order->PaymentAmount;
		$amount_tax = $order->getTaxForPrice($amount);
		//$amount = round((float)$amount + (float)$amount_tax, 2);

		//paypal profile stuff
		$nvpStr = "";
		if(!empty($order->Token))
			$nvpStr .= "&TOKEN=" . $order->Token;
		$nvpStr .="&INITAMT=" . $initial_payment . "&AMT=" . $amount . "&CURRENCYCODE=" . $pmpro_currency . "&PROFILESTARTDATE=" . $order->ProfileStartDate;
		if(!empty($amount_tax))
			$nvpStr .= "&TAXAMT=" . $amount_tax;
		$nvpStr .= "&BILLINGPERIOD=" . $order->BillingPeriod . "&BILLINGFREQUENCY=" . $order->BillingFrequency . "&AUTOBILLOUTAMT=AddToNextBilling";
		$nvpStr .= "&NOTIFYURL=" . urlencode(admin_url('admin-ajax.php') . "?action=ipnhandler");
		$nvpStr .= "&DESC=" . urlencode( apply_filters( 'pmpro_paypal_level_description', substr($order->membership_level->name . " at " . get_bloginfo("name"), 0, 127), $order->membership_level->name, $order, get_bloginfo("name")) );

		//if billing cycles are defined
		if(!empty($order->TotalBillingCycles))
			$nvpStr .= "&TOTALBILLINGCYCLES=" . $order->TotalBillingCycles;

		//if a trial period is defined
		if(!empty($order->TrialBillingPeriod))
		{
			$trial_amount = $order->TrialAmount;
			$trial_tax = $order->getTaxForPrice($trial_amount);
			$trial_amount = round((float)$trial_amount + (float)$trial_tax, 2);

			$nvpStr .= "&TRIALBILLINGPERIOD=" . $order->TrialBillingPeriod . "&TRIALBILLINGFREQUENCY=" . $order->TrialBillingFrequency . "&TRIALAMT=" . $trial_amount;
		}
		if(!empty($order->TrialBillingCycles))
			$nvpStr .= "&TRIALTOTALBILLINGCYCLES=" . $order->TrialBillingCycles;

		$nvpStr = apply_filters("pmpro_create_recurring_payments_profile_nvpstr", $nvpStr, $order);

		$this->nvpStr = $nvpStr;

		///echo str_replace("&", "&<br />", $nvpStr);
		///exit;

		$this->httpParsedResponseAr = $this->PPHttpPost('CreateRecurringPaymentsProfile', $nvpStr);

		if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"])) {
			$order->status = "success";
			$order->payment_transaction_id = urldecode($this->httpParsedResponseAr['PROFILEID']);
			$order->subscription_transaction_id = urldecode($this->httpParsedResponseAr['PROFILEID']);

			//update order
			$order->saveOrder();

			return true;
		} else  {
			$order->status = "error";
			$order->errorcode = $this->httpParsedResponseAr['L_ERRORCODE0'];
			$order->error = urldecode($this->httpParsedResponseAr['L_LONGMESSAGE0']);
			$order->shorterror = urldecode($this->httpParsedResponseAr['L_SHORTMESSAGE0']);

			return false;
		}
	}

	function cancel(&$order)
	{
		//paypal profile stuff
		$nvpStr = "";
		$nvpStr .= "&PROFILEID=" . urlencode($order->subscription_transaction_id) . "&ACTION=Cancel&NOTE=" . urlencode("User requested cancel.");

		$nvpStr = apply_filters("pmpro_manage_recurring_payments_profile_status_nvpstr", $nvpStr, $order);

		$this->httpParsedResponseAr = $this->PPHttpPost('ManageRecurringPaymentsProfileStatus', $nvpStr);

		if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"]))
		{
			$order->updateStatus("cancelled");
			return true;
		}
		else
		{
			$order->status = "error";
			$order->errorcode = $this->httpParsedResponseAr['L_ERRORCODE0'];
			$order->error = urldecode($this->httpParsedResponseAr['L_LONGMESSAGE0']) . ". " . __("Please contact the site owner or cancel your subscription from within PayPal to make sure you are not charged going forward.", 'paid-memberships-pro' );
			$order->shorterror = urldecode($this->httpParsedResponseAr['L_SHORTMESSAGE0']);

			return false;
		}
	}

	function getSubscriptionStatus(&$order)
	{
		if(empty($order->subscription_transaction_id))
			return false;

		//paypal profile stuff
		$nvpStr = "";
		$nvpStr .= "&PROFILEID=" . urlencode($order->subscription_transaction_id);

		$nvpStr = apply_filters("pmpro_get_recurring_payments_profile_details_nvpstr", $nvpStr, $order);

		$this->httpParsedResponseAr = $this->PPHttpPost('GetRecurringPaymentsProfileDetails', $nvpStr);

		if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"]))
		{
			return $this->httpParsedResponseAr;
		}
		else
		{
			$order->status = "error";
			$order->errorcode = $this->httpParsedResponseAr['L_ERRORCODE0'];
			$order->error = urldecode($this->httpParsedResponseAr['L_LONGMESSAGE0']);
			$order->shorterror = urldecode($this->httpParsedResponseAr['L_SHORTMESSAGE0']);

			return false;
		}
	}
	
	/**
	 * Filter pmpro_next_payment to get date via API if possible
	 *
	 * @since 1.8.5
	*/
	static function pmpro_next_payment($timestamp, $user_id, $order_status)
	{
		//find the last order for this user
		if(!empty($user_id))
		{
			//get last order
			$order = new MemberOrder();
			$order->getLastMemberOrder($user_id, $order_status);
			
			//check if this is a paypal express order with a subscription transaction id
			if(!empty($order->id) && !empty($order->subscription_transaction_id) && $order->gateway == "paypalexpress")
			{
				//get the subscription status
				$status = $order->getGatewaySubscriptionStatus();					
									
				if(!empty($status) && !empty($status['NEXTBILLINGDATE']))
				{
					//found the next billing date at PayPal, going to use that						
					$timestamp = strtotime(urldecode($status['NEXTBILLINGDATE']), current_time('timestamp'));
				}
				elseif(!empty($status) && !empty($status['PROFILESTARTDATE']) && $order_status == "cancelled")
				{
					//startdate is in the future and we cancelled so going to use that as the next payment date
					$startdate_timestamp = strtotime(urldecode($status['PROFILESTARTDATE']), current_time('timestamp'));
					if($startdate_timestamp > current_time('timestamp'))
						$timestamp = $startdate_timestamp;
				}
			}
		}
					
		return $timestamp;
	}

	/**
	 * PAYPAL Function
	 * Send HTTP POST Request
	 *
	 * @param	string	The API method name
	 * @param	string	The POST Message fields in &name=value pair format
	 * @return	array	Parsed HTTP Response body
	 */
	function PPHttpPost($methodName_, $nvpStr_) {
		global $gateway_environment;
		$environment = $gateway_environment;

		$API_UserName = pmpro_getOption("apiusername");
		$API_Password = pmpro_getOption("apipassword");
		$API_Signature = pmpro_getOption("apisignature");
		$API_Endpoint = "https://api-3t.paypal.com/nvp";
		if("sandbox" === $environment || "beta-sandbox" === $environment) {
			$API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
		}

		$version = urlencode('72.0');

		//NVPRequest for submitting to server
		$nvpreq = "METHOD=" . urlencode($methodName_) . "&VERSION=" . urlencode($version) . "&PWD=" . urlencode($API_Password) . "&USER=" . urlencode($API_UserName) . "&SIGNATURE=" . urlencode($API_Signature) . "&BUTTONSOURCE=" . urlencode(PAYPAL_BN_CODE) . $nvpStr_;

		//post to PayPal
		$response = wp_remote_post( $API_Endpoint, array(
				'timeout' => 60,
				'sslverify' => FALSE,
				'httpversion' => '1.1',
				'body' => $nvpreq
		    )
		);

		if ( is_wp_error( $response ) ) {
		   $error_message = $response->get_error_message();
		   die( "methodName_ failed: $error_message" );
		} else {
			//extract the response details
			$httpParsedResponseAr = array();
			parse_str(wp_remote_retrieve_body($response), $httpParsedResponseAr);

			//check for valid response
			if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
				exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
			}
		}

		return $httpParsedResponseAr;
	}

	static function pmpro_checkout_confirmed() {}
	
	
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
		//if already using paypal, ignore this	
		$setting_gateway = get_option("pmpro_gateway");
		
		if($setting_gateway == "paypal")
			return;
			
		global $pmpro_requirebilling, $gateway, $pmpro_review;
	
		//only show this if we're not reviewing and the current gateway isn't a PayPal gateway
		if ( empty( $pmpro_review ) && false === $this->pmproappe_using_paypal( $setting_gateway ) ) { ?>
	
			<div id="pmpro_payment_method" class="pmpro_checkout" <?php if(!$pmpro_requirebilling) { ?>style="display: none;"<?php } ?>>
				
				<h2><?php _e('Choose Your Payment Method', 'pmpro');?></h2>
				
				<div class="pmpro_checkout-fields">
				
					<?php if($setting_gateway != 'check') { ?>
		
						<span class="gateway_<?php echo esc_attr($setting_gateway); ?>">
							<input type="radio" name="gateway" value="<?php echo esc_attr($setting_gateway);?>" <?php if(!$gateway || $gateway == $setting_gateway) { ?>checked="checked"<?php } ?> />
							<a href="javascript:void(0);" class="pmpro_radio"><?php _e('Check Out with a Credit Card Here', 'pmpro');?></a> &nbsp;
						</span>
		
					<?php } ?>
		
					<span class="gateway_paypalexpress">
				
						<input type="radio" name="gateway" value="paypalexpress" <?php if($gateway == "paypalexpress") { ?>checked="checked"<?php } ?> />
						<a href="javascript:void(0);" class="pmpro_radio"><?php _e('Check Out with PayPal', 'pmpro');?></a> &nbsp;
				
					</span>
					
					<?php
						//integration with the PMPro Pay by Check Addon
						if(function_exists('pmpropbc_checkout_boxes')) {
							global $gateway, $pmpro_level, $pmpro_review;
							$gateway_setting = pmpro_getOption("gateway");
							$options = pmpropbc_getOptions($pmpro_level->id);
		
							//only show if the main gateway is not check and setting value == 1 (value == 2 means only do check payments)
							if($gateway_setting != "check" && $options['setting'] == 1) {
							?>
							<span class="gateway_check">
								<input type="radio" name="gateway" value="check" <?php if($gateway == "check") { ?>checked="checked"<?php } ?> />
								<a href="javascript:void(0);" class="pmpro_radio"><?php _e('Pay by Check', 'pmpropbc');?></a>
							</span>
							<?php
							}
						}
					?>
				</div>
			</div> <!--end pmpro_payment_method -->
		
			<?php //here we draw the PayPal Express button, which gets moved in place by JavaScript ?>
			
			<span id="pmpro_paypalexpress_checkout" style="display: none;">
			
				<input type="hidden" name="submit-checkout" value="1" />		
				<input type="image" class="pmpro_btn-submit-checkout" value="<?php _e('Check Out with PayPal', 'pmpro');?> &raquo;" src="<?php echo apply_filters("pmpro_paypal_button_image", "https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif");?>" />
			
			</span>
			
			<script>	
				var pmpro_require_billing = <?php if($pmpro_requirebilling) echo "true"; else echo "false";?>;
				
				//hide/show functions
				function showPayPalExpressCheckout()
				{
					jQuery('#pmpro_billing_address_fields').hide();
					jQuery('#pmpro_payment_information_fields').hide();			
					jQuery('#pmpro_submit_span').hide();
					jQuery('#pmpro_paypalexpress_checkout').show();
					
					pmpro_require_billing = false;		
				}
				
				function showCreditCardCheckout()
				{
					jQuery('#pmpro_paypalexpress_checkout').hide();
					jQuery('#pmpro_billing_address_fields').show();
					jQuery('#pmpro_payment_information_fields').show();			
					jQuery('#pmpro_submit_span').show();
					
					pmpro_require_billing = true;
				}
				
				function showFreeCheckout()
				{
					jQuery('#pmpro_billing_address_fields').hide();
					jQuery('#pmpro_payment_information_fields').hide();			
					jQuery('#pmpro_submit_span').show();
					jQuery('#pmpro_paypalexpress_checkout').hide();				
					
					pmpro_require_billing = false;	
				}
				
				function showCheckCheckout()
				{
					jQuery('#pmpro_billing_address_fields').show();
					jQuery('#pmpro_payment_information_fields').hide();			
					jQuery('#pmpro_submit_span').show();
					jQuery('#pmpro_paypalexpress_checkout').hide();				
					
					pmpro_require_billing = false;	
				}
		
				//choosing payment method
				jQuery(document).ready(function() {
		
					//move paypal express button into submit box
					jQuery('#pmpro_paypalexpress_checkout').appendTo('div.pmpro_submit');
					
					//detect gateway change
					jQuery('input[name=gateway]').click(function() {		
						var chosen_gateway = jQuery(this).val();
						if(chosen_gateway == 'paypalexpress') {
							showPayPalExpressCheckout();
						} else if(chosen_gateway == 'check') {
							showCheckCheckout();
						} else {					
							showCreditCardCheckout();
						}
					});
					
					//update radio on page load
					if(jQuery('input[name=gateway]:checked').val() == 'check') {
						showCheckCheckout();
					} else if(jQuery('input[name=gateway]:checked').val() != 'paypalexpress' && pmpro_require_billing == true) {
						showCreditCardCheckout();
					} else if(pmpro_require_billing == true) {
						showPayPalExpressCheckout();
					} else {
						showFreeCheckout();
					}
					
					//select the radio button if the label is clicked on
					jQuery('a.pmpro_radio').click(function() {
						jQuery(this).prev().click();
					});
				});
			</script>
		<?php
		}
		else
		{
		?>
			<script>
				//choosing payment method
				jQuery(document).ready(function() {		
					jQuery('#pmpro_billing_address_fields').hide();
					jQuery('#pmpro_payment_information_fields').hide();			
				});		
			</script>
		<?php
		}	
	}
	
			
}
	
	
	
	