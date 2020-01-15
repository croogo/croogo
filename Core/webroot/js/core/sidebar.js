/**
 * Admin
 *
 * for admin pages
 */
var Admin = typeof Admin == 'undefined' ? {} : Admin;

/**
 * Navigation
 *
 * @return void
 */
Admin.navigation = function () {
  var $sidebar = $('#sidebar-menu');
  var $topLevelMenus = $('#sidebar-menu > li > .hasChild');

  // no item is current, fallback to current controller index
  var $current = $('#sidebar-menu .active');
  if ($current.length == 0) {
    var selector = _.template('a.sidebar-item[href^="<%= basePath %>admin/' +
      '<%= params.plugin %>/' +
      '<%= params.controller %>"]:first');
    if ($(selector(Croogo)).addClass('current').length == 0) {
      var selector = _.template(
        'a.sidebar-item[href="<%= basePath %>admin/' + '<%= params.plugin %>"]');
      $(selector(Croogo)).addClass('current');
    }
  }

  // traverse parent elements and mark as current
  $('#sidebar-menu .active').parentsUntil('#sidebar-menu', 'ul').each(function () {
    var list = $(this).parent('li');
    list.addClass('active');

    if (window.innerWidth >= 979) {
      $('a:first', list).trigger('click');
    }

  });
};
