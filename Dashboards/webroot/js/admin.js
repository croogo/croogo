var Dashboard = {};

Dashboard.saveDashboard = function(e, ui) {
	var
		dashboard = [],
		box = null,
		serialize = function(column) {
			return function(index) {
				dashboard.push({
					'column': column,
					'order': index,
					'alias': this.id,
					'collapsed': !$(this).find('.box-content').is(':visible') ? 1 : 0
				});
			}
		};

	$('#column-0').find('.box')
		.each(serialize(0));
	$('#column-1').find('.box')
		.each(serialize(1));
	$('#column-2').find('.box')
		.each(serialize(2));

	if (ui) {
		box = ui.item;
	} else {
		box = $(this).closest('.box');
	}

	if (!box) {
		return;
	}

	box
		.find('.move-handle')
		.removeClass('icon-move')
		.addClass('icon-spinner icon-spin');

	$.post($('#dashboard-url').text(), {dashboard: dashboard}, function() {
		box
			.find('.move-handle')
			.removeClass('icon-spinner icon-spin')
			.addClass('icon-move');
	});
};

Dashboard.sortable = function(selector, saveDashboard) {
	var sortables = $(selector);
	sortables
		.sortable({
			connectWith: selector,
			handle: '.move-handle',
			placeholder: 'box-placeholder',
			forcePlaceholderSize: true,
			opacity: 0.75,
			tolerance: "pointer",
			start: function () {
				sortables.addClass('sorting');
			},
			stop: function () {
				sortables.removeClass('sorting');
			},
			update: saveDashboard
		});
};

Dashboard.collapsable = function (saveDashboard) {
	$('body').on('slide.toggle', '.dashboard-box .box-content', saveDashboard);
};

Dashboard.init = function() {
	var saveDashboard = _.debounce(Dashboard.saveDashboard, 300);
	Dashboard.sortable('.' + Croogo.themeSettings.css['dashboardClass'], saveDashboard);
	Dashboard.collapsable(saveDashboard);
}
