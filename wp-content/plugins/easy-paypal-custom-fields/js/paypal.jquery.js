
jQuery(function($) {

	// Hide the div containing the default settings
	var rpsb = $( '#rps-settings-box' ).hide();
	
	// Add a trigger to show/hide the settings box
	rpsb.before(' <p id="rps-view-toggle"><a href="#">View &amp; change default settings</a></p> ');
	
	// When the link is clicked...
	$( '#rps-view-toggle a' ).toggle( function(e) {
	
		// Change the text of the trigger from 'view... to hide...'
		$(this).html( 'Hide settings' );
		// Show the settings div
		rpsb.animate({
			'opacity' : 'toggle',
			'height' : 'toggle'
		},300);
		// Stop the default behaviour of a link
		e.preventDefault();
	
	}, function(e) {
	
		// Change the text of the trigger back
		$(this).html( 'View &amp; change default settings' );
		// Hide the settings div
		rpsb.animate({
			'opacity' : 'toggle',
			'height' : 'toggle'
		},300);
		// Yeah yeah...
		e.preventDefault();
	
	});
	
});