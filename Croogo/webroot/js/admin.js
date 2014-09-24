/**
 * Admin
 *
 * for admin pages
 */
var Admin = typeof Admin == 'undefined' ? {} : Admin;

/**
 * Gets spinner class
 */
Admin.spinnerClass = function() {
	return Admin.iconClass('spinner') + ' ' +Admin.iconClass('spin', false);
}

/**
 * Forms
 *
 * @return void
 */
Admin.form = function() {
	// Tooltips activation
	$('[rel=tooltip],*[data-title]:not([data-content]),input[title],textarea[title]').tooltip();
	if (typeof $.prototype.tipsy == 'function') {
		$('a.tooltip').tipsy({gravity: 's', html: false}); // Legacy tooltip
	}

	var ajaxToggle = function(e) {
		var $this = $(this);
		var spinnerClass = Admin.spinnerClass();
		$this.addClass(spinnerClass).find('i').attr('class', 'icon-none');
		var url = $this.data('url');
		$.post(url, function(data) {
			$this.parent().html(data);
		});
	}

	// Autocomplete
	if (typeof $.fn.typeahead_autocomplete === 'function') {
		$('input.typeahead-autocomplete').typeahead_autocomplete();
	}

	// Row Actions
	$('body')
		.on('click', 'a[data-row-action]', Admin.processLink)
		.on('click', 'a.ajax-toggle', ajaxToggle)
	;
}

/**
 * Protect forms for accidental page refresh
 */
Admin.protectForms = function() {
	var forms  = document.getElementsByClassName('protected-form');
	if (forms.length > 0) {
		var watchElements = ['input', 'select', 'textarea'];
		var ignored = ['button', '[type=submit]', '.cancel'];
		for (var i = 0; i < forms.length; i++) {
			var $form = $(forms[i]);
			var customIgnore = $form.data('ignore-elements');
			var whitelist = ignored.join(',');
			if (customIgnore) {
				whitelist += ',' + customIgnore;
			}
			$form
				.on('change', watchElements.join(','), function(e) {
					$form.data('dirty', true);
				})
				.on('click', whitelist, function(e) {
					$form.data('dirty', false);
					Croogo.Wysiwyg.resetDirty();
				});
		}

		window.onbeforeunload = function(e) {
			var dirty = false;
			for (var i = 0; i < forms.length; i++) {
				if ($(forms[i]).data('dirty') === true) {
					dirty = true;
					break;
				}
			}
			if (!dirty && !Croogo.Wysiwyg.checkDirty()) {
				return;
			}

			var confirmationMessage = 'Please save your changes';
			(e || window.event).returnValue = confirmationMessage;
			return confirmationMessage;
		};
	}
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

	if (typeof $.prototype.tabs == 'function') {
		$('.tabs').tabs(); // legacy tabs from jquery-ui
	}
	if (typeof $.prototype.elastic == 'function') {
		$('textarea').not('.content').elastic();
	}
	$("div.message").addClass("notice");
	$('#loading p').addClass('ui-corner-bl ui-corner-br');
}

/**
 * Helper callback for toggling record selection
 */
Admin.toggleRowSelection = function(selector, checkboxSelector) {
	var $selector = $(selector);
	if (typeof checkboxSelector == 'undefined') {
		checkboxSelector = "input.row-select[type='checkbox']";
	}
	$selector.on('click', function(e) {
		$(checkboxSelector).prop('checked', $selector.is(':checked'));
	});
}

/**
 * Helper method to get the proper icon class name based on theme settings
 */
Admin.iconClass = function(icon, includeDefault) {
	var result = '';
	if (typeof Croogo.themeSettings.icons[icon] === 'string') {
		icon = Croogo.themeSettings.icons[icon];
	}
	if (typeof includeDefault === 'undefined') {
		includeDefault = true;
	}
	if (includeDefault) {
		result = Croogo.themeSettings.iconDefaults['classDefault'] + ' ';
	}
	result += Croogo.themeSettings.iconDefaults['classPrefix'] + icon;
	return result.trim();
}

/**
 * Document ready
 *
 * @return void
 */
$(document).ready(function() {
	Admin.form();
	Admin.protectForms();
	Admin.extra();
});
