/**
 * AclPermissions
 *
 * for AclPermissionsController (acl plugin)
 */
var AclPermissions = {};

// row/cell templates
AclPermissions.templates = {

	permissionRow: _.template('<tr data-parent_id="<%= id %>" class="<%= classes %>"> <%= text %> </tr>'),


	controllerCell: _.template('\
<td> \
	<div class="<%= classes %>" data-alias="<%= alias %>" \
		data-level="<%= level %>" data-id="<%= id %>" > \
	<%= alias %><i class="pull-right icon-none"></i> \
	</div> \
</td>'),

	toggleButton: _.template('\
<td><i class="<%= classes.trim() %>" \
		data-aro_id="<%= aroId %>" data-aco_id="<%= acoId %>"></i> \
</td>'),

	editLinks: _.template('<td><div class="item-actions"><%= up %> <%= down %> <%= edit %> <%= del %> </div></td>')

};

/**
 * functions to execute when document is ready
 *
 * @return void
 */
AclPermissions.documentReady = function() {
	AclPermissions.permissionToggle();
	AclPermissions.tableToggle();
	$('tr:has(div.controller)').addClass('controller-row');
};

/**
 * Toggle permissions (enable/disable)
 *
 * @return void
 */
AclPermissions.permissionToggle = function() {
	$('.permission-table').one('click', '.permission-toggle:not(.permission-disabled)', function() {
		var $this = $(this);
		var acoId = $this.data('aco_id');
		var aroId = $this.data('aro_id');

		// show loader
		$this
			.removeClass('icon-ok icon-remove')
			.addClass('icon-spin icon-spinner');

		// prepare loadUrl
		var loadUrl = Croogo.basePath+'admin/acl/acl_permissions/toggle/';
		loadUrl    += acoId+'/'+aroId+'/';

		// now load it
		var target = $this.parent();
		$.post(loadUrl, null, function(data, textStatus, jqXHR) {
			target.html(data);
			AclPermissions.permissionToggle();
		});

		return false;
	});
};

/**
 * Toggle table rows (collapsible)
 *
 * @return void
 */
AclPermissions.tableToggle = function() {

	// create table rows from json
	var renderPermissions = function(data, textStatus) {
		var $el = $(this);
		var rows = '';
		var id = $el.data('id');
		for (var acoId in data.permissions) {
			text = '<td>' + acoId + '</td>';
			var aliases = data.permissions[acoId];
			for (var alias in aliases) {
				var aco = aliases[alias];
				var children = aco['children'];
				var classes = children > 0 ? 'controller perm-expand' : '';
				classes += " level-" + data.level;
				text += AclPermissions.templates.controllerCell({
					id: acoId,
					alias: alias,
					level: data.level,
					classes: classes.trim()
				});
				if (Croogo.params.controller == 'acl_permissions') {
					text += renderRoles(data.aros, acoId, aco);
				} else {
					text += AclPermissions.templates.editLinks(aco['url']);
				}
			}
			var rowClass = '';
			if (children > 0 && data.level > 0) {
				rowClass = "controller-row level-" + data.level;
			}
			rows += AclPermissions.templates.permissionRow({
				id: id,
				classes: rowClass,
				text: text
			});
		}
		var $row = $el.parents('tr');
		$(rows).insertAfter($row);
		$el.find('i').removeClass('icon-spinner icon-spin');
	};

	// create table cells for role permissions
	var renderRoles = function(aros, acoId, roles) {
		var text = '';
		for (var aroIndex in roles['roles']) {
			var cell = {
				aroId: aros[aroIndex],
				acoId: acoId,
				classes: "permission-toggle "
			};
			if (roles['children'] > 0) {
				text += '<td>&nbsp;</td>';
				continue;
			}

			var allowed = roles['roles'][aroIndex];
			if (aroIndex == 1) {
				cell.classes += "icon-ok lightgray permission-disabled";
			} else {
				if (allowed) {
					cell.classes += "icon-ok green";
				} else {
					cell.classes += "icon-remove red";
				}
			}
			text += AclPermissions.templates.toggleButton(cell);
		}
		return text;
	};

	$('.permission-table').on('click', '.controller', function() {
		var $el = $(this);
		var id = $el.data('id');
		var level = $el.data('level');

		$el.find('i').addClass('icon-spin icon-spinner');
		if ($el.hasClass('perm-expand')) {
			$el.removeClass('perm-expand').addClass('perm-collapse');
		} else {
			var children = $('tr[data-parent_id=' + id + ']');
			children.each(function() {
				var childId = $('.controller', this).data('id')
				$('tr[data-parent_id=' + childId + ']').remove();
			}).remove();
			$el.removeClass('perm-collapse').addClass('perm-expand')
				.find('i').removeClass('icon-spin icon-spinner');
			return;
		}

		var params = {
			perms: true
		};
		if (Croogo.params.controller == 'acl_actions') {
			params = $.extend(params, {
				urls: true,
				perms: false
			});
		}

		var url = Croogo.basePath + 'admin/acl/acl_permissions/index/';
		$.getJSON(url + id + '/' + level, params, function(data, textStatus) {
			renderPermissions.call($el[0], data, textStatus);
		});
	});
};

/**
 * document ready
 *
 * @return void
 */
$(document).ready(function() {
	if (Croogo.params.controller == 'acl_permissions') {
		AclPermissions.documentReady();
	}
});