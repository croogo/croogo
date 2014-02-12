/**
 * Meta
 */
var Meta = {};

/**
 * functions to execute when document is ready
 *
 * only for NodesController
 *
 * @return void
 */
Meta.documentReady = function() {
	Meta.addMeta();
	Meta.removeMeta();
}

/**
 * add meta field
 *
 * @return void
 */
Meta.addMeta = function() {
	$('a.add-meta').click(function(e) {
		var aAddMeta = $(this);
		$.get(aAddMeta.attr('href'), function(data) {
			aAddMeta.parent().find('.clear:first').before(data);
			$('div.meta a.remove-meta').unbind();
			Meta.removeMeta();
		});
		e.preventDefault();
	});
}

/**
 * remove meta field
 *
 * @return void
 */
Meta.removeMeta = function() {
	$('div.meta a.remove-meta').click(function(e) {
		var aRemoveMeta = $(this);
		if (aRemoveMeta.attr('rel') != '') {
			if (!confirm('Remove this meta field?')) {
				return false;
			}
			$.getJSON(aRemoveMeta.attr('href') + '.json', function(data) {
				if (data.success) {
					aRemoveMeta.parents('.meta').remove();
				} else {
					// error
				}
			});
		} else {
			aRemoveMeta.parents('.meta').remove();
		}
		e.preventDefault();
		return false;
	});
}

/**
 * document ready
 *
 * @return void
 */
$(document).ready(function() {
	Meta.documentReady();
});
