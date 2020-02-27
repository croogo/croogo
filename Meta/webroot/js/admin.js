/**
 * Meta
 */
var Meta = {};

Meta._spinner = '<i class="' + Admin.spinnerClass() + '"></i> ';

/**
 * functions to execute when document is ready
 *
 * only for NodesController
 *
 * @return void
 */
Meta.documentReady = function () {
  Meta.addMeta();
  Meta.removeMeta();
};

/**
 * add meta field
 *
 * @return void
 */
Meta.addMeta = function () {
  $('a.add-meta').click(function (e) {
    var aAddMeta = $(this);
    var metaCount = $('.meta-field').length;

    var spinnerClass = Admin.iconClass('spinner', false);
    aAddMeta
      .addClass('disabled')
      .prepend(Meta._spinner);
    $.get(aAddMeta.attr('href'), { count: metaCount }, function (data) {
      aAddMeta.closest('.' + Croogo.themeSettings.css.boxBodyClass).find('.meta-fields').append(data);
      $('div.meta a.remove-meta').unbind();
      Meta.removeMeta();
      aAddMeta
        .removeClass('disabled')
        .find('i.' + spinnerClass)
        .remove();
    });
    e.preventDefault();
  });
};

/**
 * remove meta field
 *
 * @return void
 */
Meta.removeMeta = function () {
  $('div.meta-field a.remove-meta').click(function (e) {
    e.preventDefault();
    var aRemoveMeta = $(this);
    var spinnerClass = Admin.iconClass('spinner', false);
    var rel = aRemoveMeta.attr('rel');
    var removeId = aRemoveMeta.parents('.meta-field').find('.meta-id').val();
    if (removeId != '' && /^[-+]?(\d+|Infinity)$/.test(removeId)) {
      if (!confirm('Remove this meta field?')) {
        return false;
      }

      aRemoveMeta
        .addClass('disabled')
        .prepend(Meta._spinner);

      var removeSpinner = function() {
        aRemoveMeta
          .removeClass('disabled')
          .find('i.' + spinnerClass)
          .remove();
      }

      $.ajax({
        url: aRemoveMeta.attr('href'),
        method: 'post',
        dataType: 'json',
        headers: {
          'X-CSRF-Token': Admin.getCookie('csrfToken'),
        },
        error: function(xhr, textStatus, errorThrown) {
          if (typeof (xhr.responseJSON.message) !== 'undefined') {
            alert(xhr.responseJSON.message);
          } else {
            alert(errorThrown);
          }
          removeSpinner();
        },
        success: function (data) {
          if (data.success) {
            var el = $('#' + rel);
            el.hide(250, function() { el.remove(); });
          } else {
            // error
          }
          removeSpinner();
        }
      });
    } else {
      var el = $('#' + rel);
      el.hide(250, function() { el.remove(); });
    }

    return false;
  });
};

/**
 * document ready
 *
 * @return void
 */
$(document).ready(function () {
  Meta.documentReady();
});
