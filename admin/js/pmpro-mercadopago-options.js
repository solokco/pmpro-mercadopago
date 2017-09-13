jQuery(function( $ ) {

	jQuery("tr.gateway_mercadopago").show();
	
	var pais_seleccionado = jQuery('#mercadopago_country').val() ;
	jQuery('.mercadopago_credenciales_' + pais_seleccionado ).show();
	
	jQuery('#mercadopago_country').on('change', function() {
		jQuery(".mercadopago_credenciales").hide();
		jQuery('.mercadopago_credenciales_' + this.value ).show();
		
	});

});