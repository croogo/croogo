var Dashboard = {};

Dashboard.debounce = function(fn, delay) {
	var timer = null;
	return function () {
		var context = this, args = arguments;
		clearTimeout(timer);
		timer = setTimeout(function () {
			fn.apply(context, args);
		}, delay);
	};
};

Dashboard.saveDashboard = function() {
	var
		dashboard = [],
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

	$.post($('#dashboard-url').text(), {dashboard: dashboard});
};

Dashboard.sortable = function(saveDashboard) {
	$('#column-0, #column-1, #column-2')
		.sortable({
			connectWith: '.sortable-column',
			handle: '.move-handle',
			placeholder: 'box-placeholder',
			opacity: 0.75,
			update: saveDashboard
		})
};

Dashboard.collapsable = function (saveDashboard) {
	$('body').on('slide.toggle', '.dashboard-box .box-content', saveDashboard);
};

$(function () {
	var saveDashboard = Dashboard.debounce(Dashboard.saveDashboard, 150);

	Dashboard.sortable(saveDashboard);
	Dashboard.collapsable(saveDashboard);
});
