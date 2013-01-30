/**
 * Javascript for Default Theme
 */
$(document).ready(function(){
	$("ul.sf-menu").supersubs({
		minWidth:    12,                                // minimum width of sub-menus in em units
		maxWidth:    27,                                // maximum width of sub-menus in em units
		extraWidth:  1                                  // extra width can ensure lines don't sometimes turn over
	}).superfish({
		delay:       400,                               // delay on mouseout
		animation:   {opacity:'show',height:'show'},    // fade-in and slide-down animation
		speed:       'fast',                            // faster animation speed
		autoArrows:  false,                             // disable generation of arrow mark-up
		dropShadows: false                              // disable drop shadows
	});

	// Prevent double form submissions on all forms to avoid unwanted requests
	$('body').on('submit', 'form', function(e) {
		var self = $(this);
		if (self.data('alreadySubmitted')) {
			e.stopImmediatePropagation();
			e.preventDefault();
		} else {
			self.data('alreadySubmitted', true);
		}
	});
});