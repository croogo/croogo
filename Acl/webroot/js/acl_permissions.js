/**
 * AclPermissions
 *
 * for AclPermissionsController (acl plugin)
 */
var AclPermissions = {};

AclPermissions._firstLoad = true;

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

AclPermissions.tabLoad = function(e) {
	var $target = $(e.target);
	var matches = (e.target.toString().match(/#.+/gi));
	var pane = matches[0];
	var alias = $target.data('alias');
	var $span = $('.' + Admin.iconClass('spinner', false), $target);
	var spinnerClass = Admin.spinnerClass();
	if ($span.length > 0) {
		$span.addClass(spinnerClass);
	} else {
		$target.append(' <span class="' + spinnerClass + '"></span>');
	};
	$(pane).load(
		Croogo.basePath + 'admin/acl/acl_permissions/',
		$.param({ root: alias }),
		function(responseText, textStatus, xhr) {
			$('span', $target).removeClass(spinnerClass);
			AclPermissions.documentReady();
		}
	);
	this._firstLoad = false;
};

/**
 * Load permissions tab using ajax
 */
AclPermissions.tabSwitcher = function() {
	$('body').on('shown.bs.tab', '#permissions-tab', AclPermissions.tabLoad);
	if (this._firstLoad) {
		AclPermissions.tabLoad({
			target: $('#permissions-tab li:first-child a').get(0)
		});
	};
}

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
		var spinnerClass = Admin.spinnerClass();

		// show loader
		$this
			.removeClass(Admin.iconClass('check-mark') + ' ' + Admin.iconClass('x-mark'))
			.addClass(spinnerClass);

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
		var spinnerClass = Admin.spinnerClass();
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
		$el.find('i').removeClass(spinnerClass);
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
				cell.classes += "lightgray permission-disabled " + Admin.iconClass("check-mark");
			} else {
				if (allowed) {
					cell.classes += "green " + Admin.iconClass("check-mark");
				} else {
					cell.classes += "red " + Admin.iconClass("x-mark");
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
		var spinnerClass = Admin.spinnerClass();

		$el.find('i').addClass(spinnerClass);
		if ($el.hasClass('perm-expand')) {
			$el.removeClass('perm-expand').addClass('perm-collapse');
		} else {
			var children = $('tr[data-parent_id=' + id + ']');
			children.each(function() {
				var childId = $('.controller', this).data('id')
				$('tr[data-parent_id=' + childId + ']').remove();
			}).remove();
			$el.removeClass('perm-collapse').addClass('perm-expand')
				.find('i').removeClass(spinnerClass);
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