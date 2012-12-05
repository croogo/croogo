/**
 * Admin
 *
 * for admin pages
 */
var Admin = {};


/**
 * Navigation
 *
 * @return void
 */
Admin.navigation = function() {
	$('ul.sf-menu').supersubs({
		minWidth: 12,
		maxWidth: 27,
		extraWidth: 1
	}).superfish({
		delay: 200,
		animation: {opacity:'show',height:'show'},
		speed: 'fast',
		autoArrows: true,
		dropShadows: false,
		disableHI: true
	});

	var $sidebar = $('#sidebar-menu');
	var $topLevelMenus = $('#sidebar-menu > li > .hasChild');

	// no item is current, fallback to current controller index
	var $current = $('.sidebar .current');
	if ($current.length == 0) {
		var selector = _.template(
			'a.sidebar-item[href="<%= basePath %>admin/' +
			'<%= params.plugin %>/' +
			'<%= params.controller %>"]'
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
		var $ul = $(this).next('ul')
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
				$ul.css({'position': 'absolute', 'margin-left': sidebarWidth + 1 + 'px', 'margin-top': '-42px'})
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
			$(this).toggle()
			dropdownClose.call(this);
		});
	});
}


/**
 * Forms
 *
 * @return void
 */
Admin.form = function() {
	// Tooltips activation
	$('[rel=tooltip],*[data-title],input[title],textarea[title]').tooltip();
	$('a.tooltip').tipsy({gravity: 's', html: false}); // Legacy tooltip

	// Row Actions
	$('body').on('click', 'a[data-row-action]', Admin.processLink);
}

/**
 * Helper to process row action links
 */
Admin.processLink = function(event) {
	var $el = $(event.currentTarget);
	var checkbox = $(event.currentTarget.attributes["href"].value);
	var form = checkbox.get(0).form;
	var action = $el.data('row-action');
	var confirmMessage = $el.data('confirm-message');
	if (confirmMessage && !confirm(confirmMessage)) {
		return false;
	}
	$('input[type=checkbox]', form).prop('checked', false);
	checkbox.prop("checked", true);
	$('#bulk-action select', form).val(action);
	form.submit();
	return false;
}

/**
 * Extra stuff
 *
 * rounded corners, striped table rows, etc
 *
 * @return void
 */
Admin.extra = function() {
	// Activates the first tab in #content
	$('#content .nav-tabs > li:first-child a').tab('show');

	// Box toggle
	$('body').on('click', '.box-title', function() {
		$(this).next().slideToggle();
	});

	$('.tabs').tabs(); // legacy tabs from jquery-ui
	$('textarea').not('.content').elastic();
	$("div.message").addClass("notice");
	$('#loading p').addClass('ui-corner-bl ui-corner-br');
}


/**
 * Document ready
 *
 * @return void
 */
$(document).ready(function() {
	Admin.navigation();
	Admin.form();
	Admin.extra();
});
