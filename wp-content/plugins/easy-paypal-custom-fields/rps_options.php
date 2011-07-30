<?php

	// Get Saved options
	$options = get_option( 'rps_myplugin_options' );
	$op_user_email = $options['username'];
	$op_currency = $options['currency'];
	$op_button = $options['buttontext'];
	$op_url = $options['url'];
	$op_button_type = $options['button-type'];
	$op_theme = $options['theme'];
	$op_post_type = $options['post-type'];
	
	// Get the array of stored values
	$rps_array = get_post_meta( $post->ID, '_rps_array', true);
	
	// Get the individual values stored in the array, check if the value is set, if not, set it to be an empty string;
	if( isset( $rps_array['show'] ) && !empty( $rps_array['show'] ) ) {
		$show = $rps_array['show'];
	} else {
		$show = 'no';
	}
	
	if( isset( $rps_array['name'] ) && !empty( $rps_array['name'] ) ) {
		$name = $rps_array['name'];
	}
	
	if( isset( $rps_array['username'] ) && !empty( $rps_array['username'] ) ) {
		$user_email = $rps_array['username'];
	} else {
		$user_email = $op_user_email;
	}
	
	if( isset( $rps_array['currency'] ) && !empty( $rps_array['currency'] ) ) {
		$currency = $rps_array['currency'];
	} else {
		$currency = $op_currency;
	}
	
	if( isset( $rps_array['buttontext'] ) && !empty( $rps_array['buttontext'] ) ) {
		$button = $rps_array['buttontext'];
	} else {
		$button = $op_button;
	}
	
	if( isset( $rps_array['button-type'] ) && !empty( $rps_array['button-type'] ) ) {
		$bt = $rps_array['button-type'];
	} else {
		$bt = $op_button_type;
	}
	
	if( isset( $rps_array['url'] ) && !empty( $rps_array['url'] ) ) {
		$url = $rps_array['url'];
	} else {
		$url = $op_url;
	}
	
	if( isset( $rps_array['theme'] ) && !empty( $rps_array['theme'] ) ) {
		$theme = $rps_array['theme'];
	} else {
		$theme = $op_theme;
	}
	
	$desc = ( isset( $rps_array['desc'] ) ) ? $rps_array['desc'] : '';
	$amount = ( isset ( $rps_array['amount'] ) ) ? $rps_array['amount'] : '';
	$postage = ( isset ( $rps_array['postage'] ) ) ? $rps_array['postage'] : '';
	$item_no = ( isset ( $rps_array['item_no'] ) ) ? $rps_array['item_no'] : '';


