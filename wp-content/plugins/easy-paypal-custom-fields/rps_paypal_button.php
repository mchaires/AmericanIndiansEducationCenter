<?php

		$rps_paypal_button = '
			<div class="paypal-form">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			';

		switch( $bt ) {
			case 'Buy now':
			default:
				$rps_paypal_button .= '
					<input type="hidden" name="cmd" value="_xclick">
					<input type="hidden" name="button_subtype" value="services">
					<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">
				';
			break;
			case 'Donations':
				$rps_paypal_button .= '
					<input type="hidden" name="cmd" value="_donations">
					<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
				';
			break;
		}
			
		$rps_paypal_button .= '<input type="hidden" name="business" value="' . antispambot( $user_email ) .'" />';
			
		if( !empty( $desc ) ) 
			$rps_paypal_button .= '<input type="hidden" name="item_name" value="' . $desc . '" />';
		
		if( !empty( $item_no ) ) 
			$rps_paypal_button .= '<input type="hidden" name="item_number" value="' . $item_no . '" />';
		
		
		$rps_paypal_button .= '
					<input type="hidden" name="amount" value="' . $amount . '" />
					<input type="hidden" name="currency_code" value="' . $currency . '" />
					<input type="hidden" name="shipping" value="' . $postage . '" />
					<input type="hidden" name="rm" value="2" />
		';
					
		if( !empty( $url ) ) 
			$rps_paypal_button .= '<input type="hidden" name="return" value="' . $url . '" />';

		if( $theme == 'light theme' ) {
			$rps_paypal_button .= '<input id="rps-paypal-button-light" type="submit" value="' . $button . '" />';
		} else if( $theme == 'dark theme' ) {
				$rps_paypal_button .= '<input id="rps-paypal-button-dark" type="submit" value="' . $button . '" />';
		}
		if( $bt == 'Buy Now' ) {
			if( $theme == 'use PayPal image - large' ) {
				$rps_paypal_button .= '<input type="image" src="' . plugins_url( '/btn_buynow_LG.gif', __FILE__ ) . '" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">';
			} else if( $op_theme == 'use PayPal image - small' ) {
				$rps_paypal_button .= '<input type="image" src="' . plugins_url( '/btn_buynow_SM.gif', __FILE__ ) . '" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">';
			}
		} else if( $bt == 'Donations' ) {
			if( $theme == 'use PayPal image - large' ) {
				$rps_paypal_button .= '<input type="image" src="' . plugins_url( '/btn_donate_LG.gif', __FILE__ ) . '" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">';
			} else if( $theme == 'use PayPal image - small' ) {
				$rps_paypal_button .= '<input type="image" src="' . plugins_url( '/btn_buynow_SM.gif', __FILE__ ) . '" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">';
			}
		}
		
		$rps_paypal_button .= '</form>
			</div>';
			
?>
