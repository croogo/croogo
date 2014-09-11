var Dashboard = {};

Dashboard.sortable = function() {
	$('#column-0, #column-1, #column-2')
		.sortable({
			connectWith: '.sortable-column',
			handle: '.move-handle',
			placeholder: 'box-placeholder',
			opacity: 0.75
		})
};

$(function () {
	Dashboard.sortable();
});
