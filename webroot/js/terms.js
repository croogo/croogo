/**
 * Terms
 *
 * for TermsController
 */
var Terms = {};

/**
 * functions to execute when document is ready
 *
 * only for TermsController
 *
 * @return void
 */
Terms.documentReady = function() {

}

/**
 * Create slugs based on title field
 *
 * @return void
 */
Terms.slug = function() {
	$("#TermTitle").slug({
		slug:'slug',
		hide: false
	});
}

/**
 * document ready
 *
 * @return void
 */
$(document).ready(function() {
	if (Croogo.params.controller == 'terms') {
		Terms.documentReady();
		if (Croogo.params.action == 'admin_add') {
			Terms.slug();
		}
	}
});