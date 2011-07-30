<?php
/*
Plugin Name: Easy PayPal Custom Fields
Plugin URI: http://richardsweeney.com/blog/easy-paypal-custom-fields/
Description: This plugin uses custom fields to make creating a PayPal button super-easy. There is no complicated shortcut syntax to remember.
Version: 1.2
Author: Richard Sweeney
Author URI: http://richardsweeney.com/
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Define current version constant
define( 'RPS_PAYPAL_VERSION', '1.2' );

// When the plugin is activated, execute the callback function rps_myplugin_activate()
register_activation_hook( __FILE__, 'rps_myplugin_activate' );

function rps_myplugin_activate() {

	// On plugin activation register the following options	
	$button = array(
		'username' => '',
		'currency' => 'EUR',
		'buttontext' => 'Checkout with Paypal',
		'theme' => 'light theme',
		'button-type' => 'Buy now',
		'url' => ''
	);
	update_option( 'rps_myplugin_options', $button );
	
	// Delete all the crap I made when people delete the plugin
	register_uninstall_hook( __FILE__, 'rps_myplugin_uninstaller' );

}


// Uninstall stuff
function rps_myplugin_uninstaller() {
	
	$options = get_option( 'rps_myplugin_options' );
	if( isset( $options['post-type'] ) )
		$op_post_type = $options['post-type'];
	
	// Get any custom post types
	$args = array(
  	'public'   => true,
	  '_builtin' => false
	); 
	$output = 'names';
	$operator = 'and';
	$custom_post_types = get_post_types( $args, $output, $operator );
	
	// Regular 'post' and 'page' post types
	$regular_post_types = array(
		'post' => 'post',
		'page' => 'page'
	);
	
	if( isset( $custom_post_types ) ) {
		// Merge the array to get ALL post types
		$all_post_types = array_merge( $regular_post_types, $custom_post_types );
	} else {
		$all_post_types = $regular_post_types;
	}
	
	//remove any additional options and custom tables
	foreach($all_post_types as $all_post_type) {
		$allposts = get_posts( 'numberposts=-1&post_type=' . $all_post_type . '&post_status=any' );
	}
	
  foreach( $allposts as $everypost ) {
    delete_post_meta( $everypost->ID, '_rps_array' );
  }

	delete_option( 'rps_myplugin_options' );
  
}


// Add stylesheet to the admin pages & the front end
add_action( 'admin_print_styles-post.php', 'rps_paypal_add_css' );
add_action( 'admin_print_styles-post-new.php', 'rps_paypal_add_css' );
add_action( 'template_redirect', 'rps_paypal_add_css' );

// Create the menu link
add_action( 'admin_menu', 'rps_myplugin_add_page' );

function rps_myplugin_add_page() {
	$rico = add_options_page(
		'Easy PayPal Custom Fields',
		'Easy PayPal Custom Fields',
		'manage_options',
		'easy-paypal-custom-fields',
		'rps_myplugin_option_page'
	);
	add_action( 'admin_print_styles-' . $rico, 'rps_paypal_add_css' );
}

// Enqueue stylesheet callback function
function rps_paypal_add_css() {
	wp_enqueue_style( 'rps_paypal_css', plugins_url( '/easy-paypal-custom-fields/css/paypal.css' ), __FILE__ );
}

// Add my JS to WP Admin
add_action( 'init', 'rps_paypal_add_js' );
// Enqueue js function
function rps_paypal_add_js() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('rps_paypal_js', plugins_url( '/easy-paypal-custom-fields/js/paypal.jquery.js' ), __FILE__, 'jquery' );
}


// Draw the option page
function rps_myplugin_option_page() {
	?>
	<div id="rps-inside" class="wrap">
	
		<div id="message" class="updated">
			<p>Thanks for downloading! If you like the plugin, please <a href="http://wordpress.org/extend/plugins/easy-paypal-custom-fields/">give me a decent rating on the WordPress plugins site</a>. It'll help more people find the plugin and will make me feel all warm inside.</p>
		</div>
	
		<div id="icon-plugins" class="icon32"></div>
		<h2>Easy PayPal Custom Fields settings</h2>
		<form action="options.php" method="post">
			<?php
				settings_fields( 'rps_myplugin_options' );
				do_settings_sections( 'rps_myplugin' );
			?>
			<br>
			<input name="Submit" type="submit" class="button-primary" id="submit" value="Save Changes" />
		</form>
		
		<?php
 			$options = get_option( 'rps_myplugin_options' );
			$buttontext = $options['buttontext'];
			$button_type = $options['button-type'];
			$op_theme = $options['theme'];
		?>
		
		<p><br /><strong>Your Button will look like this:</strong>&nbsp;&nbsp;

		<?php

		if( $op_theme == 'light theme' ) : ?>
		
			<input class="rps-pp-button" id="rps-paypal-button-light" type="submit" value="<?php echo $buttontext; ?>" />
		
		<?php	elseif( $op_theme == 'dark theme' ) : ?>
		
			<input class="rps-pp-button" id="rps-paypal-button-dark" type="submit" value="<?php echo $buttontext; ?>" />
			
		<?php
			endif;
			if( $button_type == 'Buy Now' ) :
				if( $op_theme == 'use PayPal image - large' ) :
					?>
				
				<input id="rps-pp-img" class="rps-pp-button" type="image" src="<?php echo plugins_url( '/btn_buynow_LG.gif', __FILE__ ); ?>" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				
				<?php elseif( $op_theme == 'use PayPal image - small' ) : ?>
				
					<input id="rps-pp-img" class="rps-pp-button" type="image" src="<?php echo plugins_url( '/btn_buynow_SM.gif', __FILE__ ); ?>" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				
				<?php endif;
				elseif( $button_type == 'Donations' ) :
					if( $op_theme == 'use PayPal image - large' ) : ?>
					
					<input id="rps-pp-img" class="rps-pp-button" type="image" src="<?php echo plugins_url( '/btn_donate_LG.gif', __FILE__ ); ?>" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					
				<?php elseif( $op_theme == 'use PayPal image - small' ) : ?>
				
					<input id="rps-pp-img" class="rps-pp-button" type="image" src="<?php echo plugins_url( '/btn_buynow_SM.gif', __FILE__ ); ?>" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			
				<?php endif; endif;	?>
				
				</p>
		
			</div>

	<?php
}


// Register and define the settings
add_action( 'admin_init', 'rps_myplugin_admin_init' );

function rps_myplugin_admin_init(){

	register_setting(
		'rps_myplugin_options',
		'rps_myplugin_options',
		'rps_myplugin_validate_options'
	);

	add_settings_section(
		'rps_myplugin_main',
		'Enter your default settings here',
		'rps_myplugin_section_text',
		'rps_myplugin'
	);
	
	$my_settings_fields = array(
		array(
			'id' => 'rps_myplugin_text_string', //HTML ID tag for the section
			'text' => 'PayPal username',  // Text to output for the section
			'function' => 'rps_myplugin_setting_input' // Callback function to echo the form field
		),
		array(
			'id' => 'rps_myplugin_age',
			'text' => 'Default currency',
			'function' => 'rps_myplugin_setting_currency'
		),
		array(
			'id' => 'rps_myplugin_type',
			'text' => 'Select a button type',
			'function' => 'rps_myplugin_type'
		),
		array(
			'id' => 'rps_myplugin_button_text',
			'text' => 'Custom button text (optional)',
			'function' => 'rps_myplugin_button_text'
		),
		array(
			'id' => 'rps_myplugin_return_url',
			'text' => 'Return URL (optional)',
			'function' => 'rps_myplugin_url'
		),
		array(
			'id' => 'rps_myplugin_post_types',
			'text' => 'Select on which post type to display the Button',
			'function' => 'rps_myplugin_post_type'
		),
		array(
			'id' => 'rps_myplugin_theme',
			'text' => 'Select a theme for the button',
			'function' => 'rps_myplugin_theme'
		)
	);

	
	// Settings page on which to show the section - stays the same for all fields!
	$settings_page = 'rps_myplugin';
	
	// Section of the settings page in which to show the form field as defined by the add_settings_section() function - stays the same for all fields!
	$settings_sections = 'rps_myplugin_main';

	foreach( $my_settings_fields as $my_settings_field ) {
		add_settings_field(
			$my_settings_field['id'],
			$my_settings_field['text'],
			$my_settings_field['function'],
			$settings_page,
			$settings_sections
		);
	}

}

// Draw the section header
function rps_myplugin_section_text() {
	// echo '<p><strong>Plugin Settings:</strong></p>';
}


// Display and fill the form field
function rps_myplugin_setting_input() {

	// get option 'text_string' value from the database
	$options = get_option( 'rps_myplugin_options' );
	$username = ( isset( $options['username'] ) ) ? $options['username'] : '';
	// echo the field
	?>
	<input placeholder="email@address.com" id="username" name="rps_myplugin_options[username]" type="text" value="<?php echo $username; ?>" />
	<?php
}


function rps_myplugin_setting_currency() {
	
	// Array of accepted PayPal currencies
	require( 'currencies.php' );
	
	// get option 'text_string' value from the database
	$options = get_option( 'rps_myplugin_options' );
	$currency = ( isset( $options['currency'] ) ) ? $options['currency'] : '';
	?>
		<select id="currency" name="rps_myplugin_options[currency]">
			<?php foreach($currencies as $key => $value) : ?>
				<option value="<?php echo $value; ?>" <?php selected( $currency, $value ); ?>><?php echo $value; ?> (<?php echo $key; ?>)</option>
			<?php endforeach; ?>
		</select>
	<?php
}

function rps_myplugin_type() {

	$options = get_option( 'rps_myplugin_options' );
	$op_button_type = $options['button-type'];
	$button_types = array( 'Buy Now', 'Donations' );
	?>
	<p>
		<select id="button-type" name="rps_myplugin_options[button-type]">
			<?php foreach( $button_types as $button_type ) : ?>
			<option value="<?php echo $button_type; ?>" <?php selected( $op_button_type, $button_type );	?>>
				<?php echo $button_type; ?>
			</option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php
	
}


function rps_myplugin_button_text() {

	$options = get_option( 'rps_myplugin_options' );
	$buttontext = ( isset( $options['buttontext'] ) ) ? $options['buttontext'] : '';
	?>
	<input placeholder="eg. 'Buy CD'" id="buttontext" name="rps_myplugin_options[buttontext]" type="text" value="<?php echo $buttontext; ?>" />
	<?php

}


function rps_myplugin_url() {

	$options = get_option( 'rps_myplugin_options' );
	$url = ( isset( $options['url'] ) ) ? $options['url'] : '';
	?>
	<input placeholder="The URL to return to after checkout" type="url" id="url" name="rps_myplugin_options[url]" value="<?php echo $url; ?>" />
	<?php

}


function rps_myplugin_post_type() {
	
	$options = get_option( 'rps_myplugin_options' );
	if( isset( $options['post-type'] ) )
		$op_post_type = $options['post-type'];
	
	// Get any custom post types
	$args = array(
  	'public'   => true,
	  '_builtin' => false
	); 
	$output = 'names';
	$operator = 'and';
	$custom_post_types = get_post_types( $args, $output, $operator );
	
	// Regular 'post' and 'page' post types
	$regular_post_types = array(
		'post' => 'post',
		'page' => 'page'
	);
	
	if( isset( $custom_post_types ) ) {
		// Merge the array to get ALL post types
		$all_post_types = array_merge( $regular_post_types, $custom_post_types );
	} else {
		$all_post_types = $regular_post_types;
	}
	?>
	
	<p>
 		<?php
 			// Loop through the arrays and display a checkbox for each post type
	 		foreach( $all_post_types as $post_type ) :
	 	?>
		<label>
			<input type="checkbox" name="rps_myplugin_options[post-type][]" value="<?php echo $post_type; ?>"
			<?php
				// to set the value using the checked() function for an array:
				// first check if the option value is selected (otherwise you'll get an invalid argument for the foreach loop)
				// if it is set, loop through the array to find the stored values and have the function mark them as checked
				if( isset( $op_post_type ) ) {
					foreach( $op_post_type as $checked ) {
	 					checked( $checked, $post_type );
 					}
 				}
 			?>
 			/>
			<?php echo $post_type; ?>
		</label>
		&nbsp;&nbsp;
		<?php endforeach; ?>
	</p>
	<?php

}


function rps_myplugin_theme() {

	// Choose the theme for the button
	$options = get_option( 'rps_myplugin_options' );
	$op_themes = $options['theme'];
	$themes = array( 'light theme', 'dark theme', 'use PayPal image - large', 'use PayPal image - small' );
	
	?>
	<p>
		<select id="theme" name="rps_myplugin_options[theme]">
			<?php foreach( $themes as $theme ) : ?>
			<option value="<?php echo $theme; ?>" <?php selected( $op_themes, $theme ); ?>>
				<?php echo $theme; ?>
			</option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php

}


// Validate user input
function rps_myplugin_validate_options( $input ) {

	$valid = array();

	// username
	if( empty( $input['username'] ) ) {
			add_settings_error(
			'rps_myplugin_username',
			'rps_myplugin_texterror',
			'Please enter your PayPal username',
			'error'
		);
	} elseif( !is_email( $input['username'] ) ) {
		add_settings_error(
			'rps_myplugin_username',
			'rps_myplugin_texterror',
			'Please enter a valid email address',
			'error'
		);
	} else {
		$valid['username'] = sanitize_email( $input['username'] );
	}
	
	// currency
	if( isset( $input['currency'] ) )
		$currency = trim( $input['currency'] );
	if( $currency != preg_match( '/^[A-Z]$/', $currency ) ) {
		add_settings_error(
			'rps_myplugin_currency',
			'rps_myplugin_texterror',
			'Please select a currency',
			'error'
		);
	} else {
		$valid['currency'] = $currency;
	}
	
	$button_type = $input['button-type'];
	if( $button_type == 'Buy Now' || $button_type == 'Donations' )	
		$valid['button-type'] = sanitize_text_field( $button_type );
	
	// custom button text
	$buttontext = $input['buttontext'];
	$valid['buttontext'] = sanitize_text_field( $buttontext );
	if( $valid['buttontext'] != $buttontext ) {
		add_settings_error(
			'rps_myplugin_buttontext',
			'rps_myplugin_buttonerror',
			'The custom button text may not contain illegal characters!',
			'error'
		);
	}

	// return url
	if( isset( $input['url'] ) )
		$valid['url'] = esc_url_raw( $input['url'] );
	
	// post types
	$post_types = $input['post-type'];
	if( isset( $post_types ) ) {
		foreach( $post_types as $post_type ) {
			$post_type = sanitize_text_field( $post_type );
			$valid['post-type'][] = $post_type;	
		}
	}
	
	$theme = $input['theme'];
	if( $theme == 'light theme' || $theme == 'dark theme' || $theme == 'use PayPal image - large' || $theme == 'use PayPal image - small' )
		$valid['theme'] = $input['theme'];

	return $valid;
	
}


// Create paypal meta box
add_action( 'add_meta_boxes', 'rps_paypal_meta_box' );

function rps_paypal_meta_box() {
	
	// Get the option to display on the relevant post types
	$rps_options = get_option( 'rps_myplugin_options' );
	if( isset( $rps_options['post-type'] ) )
		$post_types = $rps_options['post-type'];
	
	// Loop through the selected post types & create the meta box on the selected pages
	if( isset( $post_types) ) {
		foreach( $post_types as $post_type ) {	
			add_meta_box(
			'rps_paypal_meta',
			'Add Paypal Button',
			'rps_paypal_meta_box_callback',
			$post_type,
			'normal',
			'high'
			);
		}
	}
	
}

// See : http://codex.wordpress.org/Function_Reference/add_meta_box

// Callback function
function rps_paypal_meta_box_callback( $post ) {
	
	require( 'rps_options.php' );
	require( 'currencies.php' );
	
	?>
	
	<div id="rps-inside">
	
	<p>
		<span>Show Button:</span>
		<select name="show" id="show">
			<option value="no" <?php selected( $show , 'no' ); ?>>Don't show button</option>
			<option value="shortcode" <?php selected( $show , 'shortcode' ); ?>>Use shortcode&nbsp;&nbsp;[rps-paypal]&nbsp;&nbsp;</option>
			<option value="bottom" <?php selected( $show , 'bottom' ); ?>>At bottom of post</option>
			<option value="top" <?php selected( $show , 'top' ); ?>>At top of post</option>
		</select>
	</p>

	<p>
		<span>Product name:</span>
		<input placeholder="The name of the item for sale" type="text" name="name" id="name" value="<?php if( isset( $name) ) echo $name; ?>" />
	</p>
	
	<p><span>Product description (optional):</span></p>
		<textarea placeholder="An optional description of the item for sale - will be visible only on the paypal checkout page" name="desc" id="desc" value=""><?php echo $desc; ?></textarea>
		
	<p>
		<span>Item Number (optional):</span>
		<input type="text" name="item_no" id="item_no" value="<?php echo $item_no; ?>">
	</p>	
			
	<p>
		<span>Amount (leave blank to allow customer to enter custom amount):</span>
		<input placeholder="eg 9.99" type="text" id="amount" name="amount" value="<?php echo $amount; ?>" />
	</p>
	
	<p>
		<span>Postage (optional):</span>
		<input placeholder="eg 1.99" type="text" id="postage" name="postage" value="<?php echo $postage; ?>" />
	</p>
	
	<div id="rps-settings-box">
	
		<p>
			<span>Paypal username:</span>
			<input placeholder="email@address.com" type="email" id="username" name="username" value="<?php echo antispambot( $user_email ); ?>" />
		</p>
		
		<p>
			<span>Custom button text:</span>
			<input placeholder="eg. 'Buy CD'" type="text" id="buttontext" name="buttontext" value="<?php echo $button; ?>" />
		</p>
		
		<p>
			<span>Currency:</span>
			<select id="currency" name="currency">
				<?php foreach($currencies as $key => $value) : ?>
					<option value="<?php echo $value; ?>" <?php selected( $currency, $value ); ?>><?php echo $value; ?> (<?php echo $key; ?>)</option>
				<?php endforeach; ?>
			</select>
		</p>
		
		<?php $button_types = array( 'Buy Now', 'Donations' ); ?>
		<p>
			<span>Button Type:</span>
			<select id="button-type" name="button-type">
				<?php foreach( $button_types as $button_type ) : ?>
				<option value="<?php echo $button_type; ?>" <?php selected( $bt, $button_type ); ?>>
					<?php echo $button_type; ?>
				</option>
				<?php endforeach; ?>
			</select>
		</p>
		
		<p>
			<span>Return Url (optional):</span>
			<input placeholder="The full URL to return to after checkout" type="url" id="url" name="url" value="<?php echo $url; ?>" />
		</p>
		
		<?php $themes = array( 'light theme', 'dark theme', 'use PayPal image - large', 'use PayPal image - small' ); ?>
		<p>
			<span>Theme:</span>
			<select id="theme" name="theme">
				<?php foreach( $themes as $t ) : ?>
				<option value="<?php echo $t; ?>" <?php selected( $theme, $t ); ?>>
					<?php echo $t; ?>
				</option>
				<?php endforeach; ?>
			</select>
		</p>
	
	</div>
	
	<p><br />If you want to add the button anywhere in the post you can copy &amp; paste the shortcode
	<span id="rps-shortcode">[rps-paypal]</span>
	wherever you'd like the button to appear. <a href="http://en.support.wordpress.com/shortcodes/">What's a shortcode?</a></p>
	
	</div>
	
	<?php
}

// Save post meta
add_action('save_post', 'rps_paypal_meta_save');

function rps_paypal_meta_save( $post_id ) {
		
	// Check user permissions
	if( !current_user_can( 'edit_posts' ) )
		wp_die( "You don't have permission to do that!" );
	
	// Create the array to store the values
	$rps_array = array();

	if( isset( $_POST['show'] ) ) {
		$show = trim( $_POST['show'] );
		$rps_array['show'] = preg_replace( '/[^a-zA-Z \']/', '', $show );
	} else {
		$rps_array['show'] = 'no';
	}
	
	if( isset( $_POST['name'] ) ) {
		$name = trim( $_POST['name'] );
		$rps_array['name'] = sanitize_text_field( $name );
	} else {
		$name = '';
	}
	
	if( isset( $_POST['username'] ) && is_email( $_POST['username'] ) ) {
		$rps_array['username'] = sanitize_email( $_POST['username'] );
	}
	
	if( isset( $_POST['item_no'] ) ) {
		$rps_array['item_no'] = sanitize_text_field( $_POST['item_no'] );
	}
	
	if( isset( $_POST['desc'] ) ) {
		$desc = trim( $_POST['desc'] );
		$rps_array['desc'] = sanitize_text_field( $desc );
	}
	
	if( isset( $_POST['currency'] ) ) {
		if( preg_match( '/^[A-Z]{3}$/', $_POST['currency'] ) ) {	
			$rps_array['currency'] = $_POST['currency'];
		}
	}
	
	if( isset( $_POST['amount'] ) ) {
		$amount = trim( $_POST['amount'] );
		if( preg_match( '/^[0-9.]+$/', $amount ) ) {
			$rps_array['amount'] = $amount;
		}
	}
	
	if( isset( $_POST['postage'] ) ) {
		$postage = trim( $_POST['postage'] );
		if( preg_match( '/^[0-9.]+$/', $postage ) ) {
			$rps_array['postage'] = $postage;
		}
	}
	
	if( isset( $_POST['url'] ) ) {
		$url = trim( $_POST['url'] );
		$rps_array['url'] = esc_url_raw( $url );
	}
	
	if( isset( $_POST['buttontext'] ) ) {
		$button = trim( $_POST['buttontext'] );
		$rps_array['buttontext'] = sanitize_text_field( $button );
	}
	
	if( isset( $_POST['button-type'] ) ) {
		$bt = $_POST['button-type'];
		if( $bt == 'Buy Now' || $bt == 'Donations' )
			$rps_array['button-type'] = $bt;
	}
	
	if( isset( $_POST['theme'] ) ) {
		$theme = $_POST['theme'];
		if( $theme == 'light theme' || $theme == 'dark theme' || $theme == 'use PayPal image - large' || $theme == 'use PayPal image - small' )
			$rps_array['theme'] = $theme;
	}

	update_post_meta( $post_id, '_rps_array', $rps_array );
	
}


// Display the button

// Hook into the_content()
add_action('the_content', 'rps_button_display');

function rps_button_display( $content ) {
	
	// get the global variable $post
	global $post;
	
	require( 'rps_options.php' );

	if( $show != 'no' ) {
	
		require( 'rps_paypal_button.php' );
			
		switch( $show ) {
			case 'bottom':
				return $content . $rps_paypal_button;
			break;
			case 'top':
				return $rps_paypal_button . $content;
			case 'shortcode':
				return $content;
			break;
		}
		
	}
		else
	{
		return $content;
	}
	
}


// Create a shortcode option
add_shortcode( 'rps-paypal', 'rps_paypal_shortcode' );

function rps_paypal_shortcode() {

	global $post;
	
	require( 'rps_options.php' );
	require( 'rps_paypal_button.php' );
	return $rps_paypal_button;
	
}


?>