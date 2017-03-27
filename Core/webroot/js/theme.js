/**
 * Javascript for Default Theme
 */
$(document).ready(function(){
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