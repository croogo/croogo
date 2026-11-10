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
  var $span = $('span:first-child', $target);
  var spinnerClass = Admin.spinnerClass();
  if ($span.length > 0) {
    $span.addClass(spinnerClass);
  } else {
    $target.append(' <span class="' + spinnerClass + '"></span>');
  }
  $(pane).load(
    Croogo.basePath + 'admin/acl/permissions/',
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
  }
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
    var spinnerClass = Admin.spinnerClass();

    // show loader
    $this
      .removeClass(Admin.iconClass('check-mark') + ' ' + Admin.iconClass('x-mark'))
      .addClass(spinnerClass);

    // prepare loadUrl
    var loadUrl = Croogo.basePath+'admin/acl/permissions/toggle/';
    loadUrl    += acoId+'/'+aroId+'/';

    // now load it
    var target = $this.parent();
    $.post({
      url: loadUrl,
      headers: {
        'X-CSRF-Token': Admin.getCookie('csrfToken'),
      },
      success: function(data) {
        target.html(data);
        AclPermissions.permissionToggle();
      }
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
        if (Croogo.params.controller == 'Permissions') {
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
        var childId = $('.controller', this).data('id');
        var grandchildren = $('tr[data-parent_id=' + childId + ']');
        grandchildren.each(function() {
          var grandchildId = $('.controller', this).data('id');
          $('tr[data-parent_id=' + grandchildId + ']').remove();
        });
        $('tr[data-parent_id=' + childId + ']').remove();
      }).remove();
      $el.removeClass('perm-collapse').addClass('perm-expand')
        .find('i').removeClass(spinnerClass);
      return;
    }

    var params = {
      perms: true
    };
    if (Croogo.params.controller == 'Actions') {
      params = $.extend(params, {
        urls: true,
        perms: false
      });
    }

    var url = Croogo.basePath + 'admin/acl/permissions/index/';
    $.getJSON(url + id + '/' + level, params, function(data, textStatus) {
      renderPermissions.call($el[0], data, textStatus);
    });
  });
};

/**
 * Filter actions by name or path across the whole tree
 *
 * The tree loads one level per click, so the search runs on the server
 * (`?q=` on the index action) and its results replace the tabs until the
 * field is cleared. Toggles in the results call the same toggle action;
 * after one, clearing the field reloads the active tab so it is not stale.
 *
 * @return void
 */
AclPermissions.search = function() {
  const $form = $('#permissions-search');
  if ($form.length === 0) {
    return;
  }
  const $input = $('input[type=search]', $form);
  const $results = $('#permissions-search-results');
  const $tree = $('#permissions-tab, #permissions-tab-content');
  const text = (name) => $form.attr('data-' + name);
  let timer = null;
  let requestNo = 0;
  let toggled = false;

  const showTree = function() {
    requestNo++;
    $results.addClass('hidden').empty();
    $tree.removeClass('hidden');
    if (toggled) {
      toggled = false;
      const tab = $('#permissions-tab .nav-link.active').get(0) || $('#permissions-tab li:first-child a').get(0);
      if (tab) {
        AclPermissions.tabLoad({ target: tab });
      }
    }
  };

  const roleCell = function(result, role) {
    if (!role.aroId) {
      return '<td></td>';
    }
    let classes = 'red ' + Admin.iconClass('x-mark');
    if (role.id == 1) {
      classes = 'lightgray permission-disabled ' + Admin.iconClass('check-mark');
    } else if (result.roles[role.id]) {
      classes = 'green ' + Admin.iconClass('check-mark');
    }
    if (result.damaged) {
      // toggle() resolves this row by its lft/rght path, another action's: verdict only
      return '<td><i class="' + classes + '" title="' + _.escape(text('damaged')) + '"></i></td>';
    }
    return AclPermissions.templates.toggleButton({
      classes: 'permission-toggle ' + classes,
      aroId: role.aroId,
      acoId: result.id
    });
  };

  const render = function(data) {
    let summary = text('count').replace('{0}', data.total);
    if (data.total === 0) {
      summary = text('empty');
    } else if (data.total > data.results.length) {
      summary = text('truncated').replace('{0}', data.results.length).replace('{1}', data.total);
    }
    let html = '<p class="text-muted">' + _.escape(summary) + '</p>';
    if (data.results.length > 0) {
      let head = '<th>' + _.escape(text('id-label')) + '</th><th>' + _.escape(text('path-label')) + '</th>';
      data.roles.forEach((role) => {
        head += '<th>' + _.escape(role.title) + '</th>';
      });
      let rows = '';
      data.results.forEach((result) => {
        let path = _.escape(result.path);
        if (result.damaged) {
          path += ' <i class="text-warning fa fa-exclamation-triangle" title="' + _.escape(text('damaged')) + '"></i>';
        }
        rows += '<tr><td>' + result.id + '</td><td>' + path + '</td>';
        data.roles.forEach((role) => {
          rows += roleCell(result, role);
        });
        rows += '</tr>';
      });
      html += '<table class="table table-sm"><thead><tr>' + head + '</tr></thead><tbody>' + rows + '</tbody></table>';
    }
    $results.html(html);
  };

  const run = function() {
    const term = String($input.val()).trim();
    if (term.length < 2) {
      showTree();
      return;
    }
    const current = ++requestNo;
    $.getJSON(Croogo.basePath + 'admin/acl/permissions/index', { q: term })
      .done((data) => {
        if (current !== requestNo) {
          return;
        }
        $tree.addClass('hidden');
        $results.removeClass('hidden');
        render(data);
      })
      .fail(() => {
        if (current === requestNo) {
          $results.removeClass('hidden').text(text('error'));
        }
      });
  };

  $input.on('input', () => {
    clearTimeout(timer);
    timer = setTimeout(run, 300);
  });
  $input.on('keydown', (e) => {
    if (e.key === 'Escape') {
      clearTimeout(timer);
      $input.val('');
      showTree();
    }
  });
  $form.on('submit', () => {
    clearTimeout(timer);
    run();
    return false;
  });

  $results.on('click', '.permission-toggle:not(.permission-disabled)', function() {
    const $icon = $(this);
    const $cell = $icon.parent();
    if ($cell.data('busy')) {
      return false;
    }
    $cell.data('busy', true);
    $icon
      .removeClass(Admin.iconClass('check-mark') + ' ' + Admin.iconClass('x-mark'))
      .addClass(Admin.spinnerClass());
    // before the request: clearing the filter while it runs must still reload the tree
    toggled = true;
    $.post({
      url: Croogo.basePath + 'admin/acl/permissions/toggle/' + $icon.data('aco_id') + '/' + $icon.data('aro_id') + '/',
      headers: {
        'X-CSRF-Token': Admin.getCookie('csrfToken'),
      }
    })
      .done((html) => {
        $cell.html(html);
      })
      .fail(() => {
        $cell.text(text('error'));
      })
      .always(() => {
        $cell.data('busy', false);
      });
    return false;
  });
};

/**
 * document ready
 *
 * @return void
 */
$(document).ready(function() {
  if (Croogo.params.controller == 'permissions') {
    AclPermissions.documentReady();
  }
});
