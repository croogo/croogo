/**
 * Meta
 */
var Meta = {};

Meta._spinner = ' <i class="icon-spinner icon-spin"></i>';

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
		aAddMeta.after(Meta._spinner);
		$.get(aAddMeta.attr('href'), function(data) {
			aAddMeta.parent().find('.clear:first').before(data);
			$('div.meta a.remove-meta').unbind();
			Meta.removeMeta();
			aAddMeta.siblings('i.icon-spinner').remove();
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
			aRemoveMeta.after(Meta._spinner);
			$.getJSON(aRemoveMeta.attr('href') + '.json', function(data) {
				if (data.success) {
					aRemoveMeta.parents('.meta').remove();
				} else {
					// error
				}
				aRemoveMeta.siblings('i.icon-spinner').remove();
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
