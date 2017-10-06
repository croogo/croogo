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
  var $current = $('.nav-sidebar .current');
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
  $($current.selector).parentsUntil('.nav-sidebar', 'ul').each(function () {
    $(this).siblings('a.sidebar-item').addClass('current')
  });
  if (window.innerWidth >= 979) {
    $('.current', $sidebar)
      .parents('.sub-nav')
      .last()
      .toggle()
      .siblings('.sidebar-item')
      .addClass('current')
  }

  var dropdownOpen = function () {
    $(this)
      .addClass('dropdown-open')
      .removeClass('dropdown-close')
      .siblings('.sidebar-item')
      .addClass('dropdown-open')
      .removeClass('dropdown-close');
  };

  var dropdownClose = function () {
    $(this)
      .addClass('dropdown-close')
      .removeClass('dropdown-open')
      .siblings('.sidebar-item')
      .addClass('dropdown-close')
      .removeClass('dropdown-open');
  };

  $topLevelMenus.on('click blur', function (e) {
    var $this = $(this);
    var $ul = $(this).next('ul');
    var sidebarWidth = $sidebar.width();

    if (e.type == 'blur' && window.innerWidth > 979) {
      return;
    }

    if ($ul.is(':visible')) {

      var onComplete = function () {
        dropdownClose.call($ul.get(0));
        $ul.css({'margin-left': sidebarWidth + 'px', 'margin-top': 'inherit'})
      }

      if (window.innerWidth <= 979) {
        $ul.removeAttr('z-index').fadeOut('fast');
      } else {
        $ul.slideUp('fast', onComplete);
      }
    } else {
      $topLevelMenus.siblings('ul:visible').slideUp('fast', function () {
        dropdownClose.call(this);
      });
      dropdownOpen.call(this);
      if (window.innerWidth <= 979) {
        if (e.type == 'click') {
          $ul.css(
            {'position': 'absolute', 'margin-left': sidebarWidth + 1 + 'px', 'margin-top': '-42px'});
          $ul.css({'z-index': 99}).fadeIn('fast');
        }
      } else {
        $ul.css({'margin-left': 0, 'position': 'relative', 'margin-top': '0px'});
        $ul.slideDown('fast');
      }
    }
    e.stopPropagation();
    return false;
  });

  $(window).on('resize', function () {
    $('#sidebar-menu > li ul:visible').each(function () {
      $(this).css({'position': 'relative', 'margin-top': '0px'}).toggle();
      dropdownClose.call(this);
    });
  });
}

/**
 * Document ready
 *
 * @return void
 */
$(document).ready(function () {
  Admin.navigation();
});
