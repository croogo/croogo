/**
 * Links
 *
 * for LinksController
 */
var Links = {};

/**
 * Create slugs based on title field
 *
 * @return void
 */
Links.slug = function() {
	$("#LinkTitle").slug({
		slug: 'class',
		hide: false
	});
}

/**
 * document ready
 *
 * @return void
 */
$(document).ready(function() {
	if (Croogo.params.controller == 'links') {
		if (['admin_add', 'admin_edit'].indexOf(Croogo.params.action) >= 0) {
			Links.slug();
		}
	}
});
