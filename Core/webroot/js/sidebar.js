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
Admin.navigation = function() {
	var $sidebar = $('#sidebar-menu');
	var $topLevelMenus = $('#sidebar-menu > li > .hasChild');

	// no item is current, fallback to current controller index
	var $current = $('.sidebar .current');
	if ($current.length == 0) {
		var selector = _.template(
			'a.sidebar-item[href^="<%= basePath %>admin/' +
			'<%= params.plugin %>/' +
			'<%= params.controller %>"]:first'
		);
		if ($(selector(Croogo)).addClass('current').length == 0) {
			var selector = _.template(
				'a.sidebar-item[href="<%= basePath %>admin/' +
				'<%= params.plugin %>"]'
			);
			$(selector(Croogo)).addClass('current');
		}
	}
	// traverse parent elements and mark as current
	$($current.selector).parentsUntil('.sidebar', 'ul').each(function() {
		$(this).siblings('a.sidebar-item').addClass('current')
	});
	if (window.innerWidth >= 979) {
		$topLevelMenus.parent().find('> .current').next('ul').toggle();
	}

	var dropdownOpen = function() {
		$(this)
			.addClass('dropdown-open')
			.removeClass('dropdown-close')
			.siblings('.sidebar-item')
			.addClass('dropdown-open')
			.removeClass('dropdown-close');
	};

	var dropdownClose = function() {
		$(this)
			.addClass('dropdown-close')
			.removeClass('dropdown-open')
			.siblings('.sidebar-item')
			.addClass('dropdown-close')
			.removeClass('dropdown-open');
	};

	$topLevelMenus.on('click blur', function(e) {
		var $this = $(this);
		var $ul = $(this).next('ul');
		var sidebarWidth = $sidebar.width();

		if (e.type == 'blur' && window.innerWidth > 979) {
			return;
		}

		if ($ul.is(':visible')) {

			var onComplete = function() {
				dropdownClose.call($ul.get(0));
				$ul.css({'margin-left': sidebarWidth + 'px', 'margin-top': 'inherit'})
			}

			if (window.innerWidth <= 979) {
				$ul.hide('fade', 'fast', onComplete);
			} else {
				$ul.slideUp('fast', onComplete);
			}
		} else {
			$topLevelMenus.siblings('ul:visible').slideUp('fast', function() {
				dropdownClose.call(this);
			});
			dropdownOpen.call(this);
			if (window.innerWidth <= 979) {
				$ul.css({'position': 'absolute', 'margin-left': sidebarWidth + 1 + 'px', 'margin-top': '-42px'});
				$ul.show('fade', 'fast');
			} else {
				$ul.css({'margin-left': 0, 'position': 'relative'});
				$ul.slideDown('fast');
			}
		}
		e.stopPropagation();
		return false;
	});

	$(window).on('resize', function() {
		$('#sidebar-menu > li ul:visible').each(function() {
			$(this).toggle();
			dropdownClose.call(this);
		});
	});
}


/**
 * Document ready
 *
 * @return void
 */
$(document).ready(function() {
	Admin.navigation();
});
