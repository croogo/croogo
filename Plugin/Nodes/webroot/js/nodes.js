/**
 * Nodes
 *
 * for NodesController
 */
var Nodes = {};

/**
 * functions to execute when document is ready
 *
 * only for NodesController
 *
 * @return void
 */
Nodes.documentReady = function() {
	Nodes.filter();
	Nodes.addMeta();
	Nodes.removeMeta();
}

/**
 * Submits form for filtering Nodes
 *
 * @return void
 */
Nodes.filter = function() {
	$('.nodes div.actions a.filter').click(function() {
		$('.nodes div.filter').slideToggle();
		return false;
	});

	$('#FilterAddForm div.submit input').click(function() {
		$('#FilterAddForm').submit();
		return false;
	});

	$('#FilterAdminIndexForm').submit(function() {
		var filter = '';
		var q='';

		// type
		if ($('#FilterType').val() != '') {
			filter += 'type:' + $('#FilterType').val() + ';';
		}

		// status
		if ($('#FilterStatus').val() != '') {
			filter += 'status:' + $('#FilterStatus').val() + ';';
		}

		// promoted
		if ($('#FilterPromote').val() != '') {
			filter += 'promote:' + $('#FilterPromote').val() + ';';
		}

		//query string
		if($('#FilterQ').val() != '') {
			q=$('#FilterQ').val();
		}
		var loadUrl = Croogo.basePath + 'admin/nodes/nodes/index/';
		if (filter != '') {
			loadUrl += 'filter:' + filter;
		}
		if (q != '') {
			if (filter == '') {
				loadUrl +='q:'+q;
			} else {
				loadUrl +='/q:'+q;
			}
		}

		window.location = loadUrl;
		return false;
	});
}

/**
 * add meta field
 *
 * @return void
 */
Nodes.addMeta = function() {
	$('a.add-meta').click(function(e) {
		var aAddMeta = $(this);
		$.get(aAddMeta.attr('href'), function(data) {
			aAddMeta.parent().find('.clear:first').before(data);
			$('div.meta a.remove-meta').unbind();
			Nodes.removeMeta();
		});
		e.preventDefault();
	});
}

/**
 * remove meta field
 *
 * @return void
 */
Nodes.removeMeta = function() {
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
 * Create slugs based on title field
 *
 * @return void
 */
Nodes.slug = function() {
	$("#NodeTitle").slug({
		slug:'slug',
		hide: false
	});
}

Nodes.confirmProcess = function(confirmMessage) {
	var action = $('#NodeAction :selected');
	if (action.val() == '') {
		confirmMessage = 'Please select an action';
	}
	if (confirmMessage == undefined) {
		confirmMessage = 'Are you sure?';
	} else {
		confirmMessage = confirmMessage.replace(/\%s/, action.text());
	}
	if (confirm(confirmMessage)) {
		action.get(0).form.submit();
	}
	return false;
}

/**
 * document ready
 *
 * @return void
 */
$(document).ready(function() {
	if (Croogo.params.controller == 'nodes') {
		Nodes.documentReady();
		if (Croogo.params.action == 'admin_add') {
			Nodes.slug();
		}
	}
});
