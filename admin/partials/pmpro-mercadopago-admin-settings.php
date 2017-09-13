<tr class="pmpro_settings_divider gateway gateway_mercadopago" <?php if($gateway != "mercadopago") { ?>style="display: none;"<?php } ?>>
	<td colspan="2">
		<?php _e('Mercadopago Settings', 'pmpro-mercadopago'); ?>
	</td>
</tr>

<tr class="gateway gateway_mercadopago" <?php if($gateway != "mercadopago") { ?>style="display: none;"<?php } ?>>
	<th scope="row" valign="top">
		<label for="mercadopago_country"><?php _e('Pais', 'paid-memberships-pro' );?>:</label>
	</th>
	
	<td>
		<select id="mercadopago_country" name="pais_mercadopago">
			<option value="argentina" <?php selected(pmpro_getOption('pais_mercadopago'), "argentina");?>>Argentina</option>
			<option value="brasil" <?php selected(pmpro_getOption('pais_mercadopago'), "brasil");?>>Brasil</option>
			<option value="chile" <?php selected(pmpro_getOption('pais_mercadopago'), "chile");?>>Chile</option>
			<option value="colombia" <?php selected(pmpro_getOption('pais_mercadopago'), "colombia");?>>Colombia</option>
			<option value="mexico" <?php selected(pmpro_getOption('pais_mercadopago'), "mexico");?>>Mexico</option>
			<option value="peru" <?php selected(pmpro_getOption('pais_mercadopago'), "peru");?>>Peru</option>
			<option value="uruguay" <?php selected(pmpro_getOption('pais_mercadopago'), "uruguay");?>>Uruguay</option>
			<option value="venezuela" <?php selected(pmpro_getOption('pais_mercadopago'), "venezuela");?>>Venezuela</option>
		</select>
		
		<p><?php _e( "Por <strong>restricciones de Mercadopago</strong> Los pagos <strong>recurrentes o suscripciones</strong> sólo están disponibles para <strong>Argentina, Brasil y México</strong>" , "pmpro-mercadopago") ?></p>
		
		<ul>
			<li class="mercadopago_credenciales mercadopago_credenciales_argentina"><a href="https://www.mercadopago.com/mla/account/credentials?type=basic" 	target="_blank"><?php _e( "Haz click aqui para conseguir las credenciales de Mercadopago - " , "pmpro-mercadopago") ?> Argentina</a> </li>
			<li class="mercadopago_credenciales mercadopago_credenciales_brasil"><a href="https://www.mercadopago.com/mlb/account/credentials?type=basic" 		target="_blank"><?php _e( "Haz click aqui para conseguir las credenciales de Mercadopago - " , "pmpro-mercadopago") ?> Brasil</a>	   </li>
			<li class="mercadopago_credenciales mercadopago_credenciales_chile"><a href="https://www.mercadopago.com/mlc/account/credentials?type=basic" 		target="_blank"><?php _e( "Haz click aqui para conseguir las credenciales de Mercadopago - " , "pmpro-mercadopago") ?> Chile</a>	   </li>
			<li class="mercadopago_credenciales mercadopago_credenciales_colombia"><a href="https://www.mercadopago.com/mco/account/credentials?type=basic" 	target="_blank"><?php _e( "Haz click aqui para conseguir las credenciales de Mercadopago - " , "pmpro-mercadopago") ?> Colombia</a>  </li>
			<li class="mercadopago_credenciales mercadopago_credenciales_mexico"><a href="https://www.mercadopago.com/mlm/account/credentials?type=basic" 		target="_blank"><?php _e( "Haz click aqui para conseguir las credenciales de Mercadopago - " , "pmpro-mercadopago") ?> Mexico</a>	   </li>
			<li class="mercadopago_credenciales mercadopago_credenciales_peru"><a href="https://www.mercadopago.com/mpe/account/credentials?type=basic" 		target="_blank"><?php _e( "Haz click aqui para conseguir las credenciales de Mercadopago - " , "pmpro-mercadopago") ?> Peru</a>	   </li>
			<li class="mercadopago_credenciales mercadopago_credenciales_uruguay"><a href="https://www.mercadopago.com/mlu/account/credentials?type=basic" 		target="_blank"><?php _e( "Haz click aqui para conseguir las credenciales de Mercadopago - " , "pmpro-mercadopago") ?> Uruguay</a>   </li>
			<li class="mercadopago_credenciales mercadopago_credenciales_venezuela"><a href="https://www.mercadopago.com/mlv/account/credentials?type=basic" 	target="_blank"><?php _e( "Haz click aqui para conseguir las credenciales de Mercadopago - " , "pmpro-mercadopago") ?> Venezuela</a> </li>
		</ul>
		
		<a id="mercadopago_sandbox_cards" href="https://www.mercadopago.com.ve/developers/en/solutions/payments/custom-checkout/test-cards/" target="_blank" style="display: none;"><?php _e( "Ver tarjetas de credito para probar el gateway" , "pmpro-mercadopago") ?></a>

	</td>
	
</tr>

<tr class="gateway gateway_mercadopago" <?php if($gateway != "mercadopago") { ?>style="display: none;"<?php } ?>>

	<th scope="row" valign="top"> 
        <label for="mercadopago_email"><?php _e('Email', 'paid-memberships-pro' );?>:</label> 
    </th> 
    
    <td> 
        <input type="text" id="mercadopago_email" name="mercadopago_email" size="60" value="<?php echo esc_attr($values['mercadopago_email'])?>" /> 
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