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

Links.reloadParents = function(event) {
	var query = { menu_id: event.currentTarget.value };
	var url = Croogo.basePath + 'admin/menus/links/index.json';
	$.getJSON(url, query, function(data, text) {
		if (typeof data.linksTree === 'undefined') {
			return;
		}
		var $selectParent = $('#LinkParentId');
		var options = '<option></option>';
		var theParent = null;
		var linkId = $('#LinkId').val();
		$selectParent.empty();
		for (key in data.linksTree) {
			var title = data.linksTree[key];
			for (var i in data.menu.Link) {
				var currentLink = data.menu.Link[i];
				if (currentLink.id == linkId) {
					theParent = currentLink.parent_id;
				}
			}
			options += '<option';
			if (key == theParent) {
				options += ' selected="selected"';
			}
			options += ' value="' + key + '">' + title + '</option>';
		}
		$selectParent.html(options);
	});
}

/**
 * document ready
 *
 * @return void
 */
$(function() {
	if (Croogo.params.controller == 'links') {
		if (['admin_add', 'admin_edit'].indexOf(Croogo.params.action) >= 0) {
			Links.slug();
		}
	}

	$('#LinkMenuId').on('change', Links.reloadParents);

	Admin.toggleRowSelection('#LinkCheckAll');
});
