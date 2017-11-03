/**
 * Admin
 *
 * for admin pages
 */
var Admin = typeof Admin == 'undefined' ? {} : Admin;

/**
 * Gets spinner class
 */
Admin.spinnerClass = function () {
  return Admin.iconClass('spinner') + ' ' + Admin.iconClass('spin', false);
}

/**
 * Forms
 *
 * @return void
 */
Admin.form = function () {
  // Tooltips activation
  $('[rel=tooltip],*[data-title]:not([data-content]),input[title],textarea[title]').tooltip();

  var ajaxToggle = function (e) {
    var $this = $(this);
    var spinnerClass = Admin.spinnerClass();
    $this.find('i').attr('class', spinnerClass)
    var url = $this.data('url');
    $.post(url, function (data) {
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
    .on('click', 'a.ajax-toggle', ajaxToggle);
}

/**
 * Protect forms for accidental page refresh
 */
Admin.protectForms = function () {
  var forms = document.getElementsByClassName('protected-form');
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
        .on('change', watchElements.join(','), function (e) {
          $form.data('dirty', true);
        })
        .on('click', whitelist, function (e) {
          $form.data('dirty', false);
          if (typeof Croogo.Wysiwyg.resetDirty == 'function') {
            Croogo.Wysiwyg.resetDirty();
          }
        });
    }

    window.onbeforeunload = function (e) {
      var dirty = false;
      for (var i = 0; i < forms.length; i++) {
        if ($(forms[i]).data('dirty') === true) {
          dirty = true;
          break;
        }
      }
      if (!dirty) {
        if (typeof Croogo.Wysiwyg.isDirty == 'function' && !Croogo.Wysiwyg.isDirty()) {
          return;
        } else {
          return;
        }
      }

      var confirmationMessage = 'Please save your changes';
      (
      e || window.event
      ).returnValue = confirmationMessage;
      return confirmationMessage;
    };
  }
}

Admin.formFeedback = function () {
  $('body').on('submit', 'form', function (el) {
    var submitButtons = $(this).find('[type=submit]');
    submitButtons
      .addClass('disabled');

    if (el.originalEvent.explicitOriginalTarget) {
      var $button = $(el.originalEvent.explicitOriginalTarget);
      if ($button.find('i').length == 0) {
        $button
          .prepend(' ')
          .prepend($('<i />').addClass(Admin.spinnerClass()));
      } else {
        $button.find('i').attr('class', Admin.spinnerClass());
      }
    }
  });

  var activateErrorTab = function(e) {
    var pane = $(e.target).closest('.tab-pane').get(0);
    var selector = 'a[href="#' + pane.attributes['id'].value + '"]';
    $('#content .nav-tabs').find(selector).tab('show')
  };
  $('form input').on('invalid', _.debounce(activateErrorTab, 150))
}

/**
 * Helper to process row action links
 */
Admin.processLink = function (event) {
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

Admin.removeHash = function() {
  var scrollV, scrollH, loc = window.location;
  if ("pushState" in history)
    history.pushState("", document.title, loc.pathname + loc.search);
  else {
    // Prevent scrolling by storing the page's current scroll offset
    scrollV = document.body.scrollTop;
    scrollH = document.body.scrollLeft;

    loc.hash = "";

    // Restore the scroll offset, should be flicker free
    document.body.scrollTop = scrollV;
    document.body.scrollLeft = scrollH;
  }
}

/**
 * Extra stuff
 *
 * rounded corners, striped table rows, etc
 *
 * @return void
 */
Admin.extra = function () {
  var hash = document.location.hash;
  var $tabs = $('#content .nav-tabs');
  if (hash && hash.match("^#tab_")) {
    // Activates tab if hash starting with tab_* is given
    $tabs.find('a[href="' + hash.replace('tab_', '') + '"]').tab('show');
    Admin.removeHash();
  } else {
    // Activates the first tab in #content by default
    $tabs.find('li:first-child a').tab('show');
  }

  // Apply buttons jump to current tab for persistence
  $('#content [name="_apply"]').click(function () {
    var activeTab = $tabs.find('.active[data-toggle=tab]').attr('href');
    var form = $('#content form:first');
    var action = form.attr('action').split('#')[0];
    form.attr('action', action + activeTab.replace('#', '#tab_'));
  });

  if (typeof $.prototype.elastic == 'function') {
    $('textarea').not('.content').elastic();
  }
  $("div.message").addClass("notice");
  $('#loading p').addClass('ui-corner-bl ui-corner-br');

  if (typeof $.fn.ekkoLightbox !== 'undefined') {
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox();
    });
  }

  if (typeof $.fn.select2 !== 'undefined') {
    $('select:not(".no-select2")').select2({
      dropdownAutoWidth: true,
      theme: 'bootstrap'
    });
  }
}

/**
 * Initialize boxes to enable to toggling Box content
 */
Admin.slideBoxToggle = function () {
  var iconMinus = Admin.iconClass('minus', false);
  var iconPlus = Admin.iconClass('plus', false);
  $('body').on('click', '.box-title', function () {
    $(this)
      .next().slideToggle(function () {
      $(this).trigger('slide.toggle');
    }).end()
      .find(iconMinus)
      .switchClass(iconMinus, iconPlus).end()
      .find(iconPlus)
      .switchClass(iconPlus, iconMinus);
  });
};

/**
 * Helper callback for toggling record selection
 */
Admin.toggleRowSelection = function (selector, checkboxSelector) {
  var $selector = $(selector);
  if (typeof checkboxSelector == 'undefined') {
    checkboxSelector = "input.row-select[type='checkbox']";
  }
  $selector.on('click', function (e) {
    $(checkboxSelector).prop('checked', $selector.is(':checked'));
  });
}

/**
 * Helper method to get the proper icon class name based on theme settings
 */
Admin.iconClass = function (icon, includeDefault) {
  var result = '';
  if (typeof Croogo.themeSettings.icons[icon] === 'string') {
    icon = Croogo.themeSettings.icons[icon];
  }
  if (typeof includeDefault === 'undefined') {
    includeDefault = true;
  }
  if (includeDefault) {
    result = Croogo.themeSettings.iconDefaults['iconSet'] + ' ';
  }
  result += Croogo.themeSettings.iconDefaults['iconSet'] + '-' + icon;
  return result.trim();
}

Admin.dateTimeFields = function(datePickers) {
  datePickers = typeof datePickers !== 'undefined' ? datePickers : $('[role=datetime-picker]');

  datePickers.each(function () {

    var picker = $(this);
    var date = null;

    if (picker.data('timestamp') && picker.data('timezone')) {
      var timezone = picker.data('timezone');
      date = moment(picker.data('timestamp') * 1000);
      date = date.tz(timezone)
    }

    var sDate = date ? date : picker.val()

    picker.on('dp.change', function(e) {
      var sDate = ""
      date = moment(e.date);
      if (date.isValid()) {
        date.tz('UTC').locale('UTC');
        sDate = date.format('YYYY-MM-DD HH:mm:ss');
      }
      $('#' + picker.data('related')).val(sDate);
    });

    var dpOptions = {
      locale: picker.data('locale'),
      format: picker.data('format'),
      date: date ? date : picker.val(),
      icons: {
        time: 'fa fa-clock-o',
        date: 'fa fa-calendar',
        up: 'fa fa-chevron-up',
        down: 'fa fa-chevron-down',
        previous: 'fa fa-chevron-left',
        next: 'fa fa-chevron-right',
        today: 'fa fa-screenshot',
        clear: 'fa fa-trash',
        close: 'fa fa-remove'
      }
    }
    if (picker.data('mindate')) {
      dpOptions.minDate = picker.data('mindate');
    }
    if (picker.data('maxdate')) {
      dpOptions.maxDate =  picker.data('maxdate');
    }
    picker.datetimepicker(dpOptions);
  });
}
