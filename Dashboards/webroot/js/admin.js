var Dashboard = {};

Dashboard.saveDashboard = function(e, ui) {
  var
    dashboard = [],
    box = null,
    serialize = function(column) {
      return function(index) {
        dashboard.push({
          'column': column,
          'weight': index,
          'alias': this.id,
          'collapsed': !$(this).find('.card-block').is(':visible') ? 1 : 0
        });
      }
    };

  $('#column-0').find('.dashboard-card')
    .each(serialize(0));
  $('#column-1').find('.dashboard-card')
    .each(serialize(1));
  $('#column-2').find('.dashboard-card')
    .each(serialize(2));

  if (ui) {
    box = ui.item;
  } else {
    box = $(this).closest('.dashboard-card');
  }

  if (!box) {
    return;
  }

  var collapsed = !box.find('.card-block').is(':visible') ? 1 : 0;
  $.post($('#dashboard-url').text(), {dashboard: dashboard}, function() {
    box
      .find('.toggle-icon .fa')
      .removeClass('fa-spinner fa-spin')
      .addClass(collapsed ? 'fa-plus' : 'fa-minus')
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
  var selector = '.dashboard-card .card-block';
  $('body')
    .on('show.bs.collapse hide.bs.collapse', selector, function(e, ui) {
      if (ui) {
        box = ui.item;
      } else {
          box = $(this).closest('.dashboard-card');
      }

      var collapsed = !box.find('.card-block').is(':visible') ? 1 : 0;
      box
        .find('.toggle-icon .fa')
        .removeClass(collapsed ? 'fa-plus' : 'fa-minus')
        .addClass('fa-spinner fa-spin');
    });

  $('body')
    .on('shown.bs.collapse hidden.bs.collapse', selector, saveDashboard);
};

Dashboard.init = function() {
  var saveDashboard = _.debounce(Dashboard.saveDashboard, 300);
  Dashboard.sortable('.' + Croogo.themeSettings.css['dashboardClass'], saveDashboard);
  Dashboard.collapsable(saveDashboard);
}
