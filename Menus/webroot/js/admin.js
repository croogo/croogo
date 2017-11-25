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


Links.setupSelect2 = function(selector) {
  var link = $(selector);

  var initSelect2 = function(link, opt, vals) {
    if (opt) {
      link.html(opt).change()
    }
    setTimeout(function() {
      if (vals) {
        link.val(vals);
      }
      link.select2({
        tags: true,
        tokenSeparators: [' '],
        allowClear: true,
        selectOnClose: true,
        theme: 'bootstrap',
        placeholder: {
          id: '-1',
          text: ''
        }
      })
    }, 1);
  };

  var makeOption = function(val) {
    return '<option data-select2-tag="true" selected="selected" ' +
      'value="' + val + '">' + decodeURIComponent(val) + '</option>';
  }

  link
    .on('chooserSelect', function(e, data) {
      var opt = "";
      var values = [];
      var rel = $(data).attr('rel');
      if (
        rel.indexOf('plugin:') >= 0 ||
        rel.indexOf('controller:') >= 0 ||
        rel.indexOf('action:') >= 0
      ) {
        var arr = rel.split('/');
        for (var i in arr) {
          opt += makeOption(arr[i]);
          values.push(arr[i]);
        }
      } else {
        opt = makeOption(rel);
        values.push(rel);
      }
      initSelect2(link, opt, values);
    })

    initSelect2(link);
};

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
  Links.setupSelect2('#link')

  Admin.toggleRowSelection('#LinksCheckAll');
});
