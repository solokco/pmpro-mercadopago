jQuery(function( $ ) {
	
	var gateway_seleccionado = jQuery('select#gateway').val() ;
	var pais_seleccionado = jQuery('#mercadopago_country').val() ;
	var gateway_environment = jQuery('select[name="gateway_environment"]').val() ;
	
	if ( gateway_environment == "sandbox") {
		jQuery("#mercadopago_sandbox_cards").show();
	}
	
	if ( gateway_seleccionado == "mercadopago") {
	
	}
		
	jQuery('.mercadopago_credenciales_' + pais_seleccionado ).show();
	
	jQuery('select[name="gateway_environment"]').on('change', function() {
		
		if ( this.value == "sandbox") {
			jQuery("#mercadopago_sandbox_cards").show();
		} else {
			jQuery("#mercadopago_sandbox_cards").hide();
		}
		
	});
	
	jQuery('#mercadopago_country').on('change', function() {
		jQuery(".mercadopago_credenciales").hide();
		jQuery('.mercadopago_credenciales_' + this.value ).show();
		
	});

});