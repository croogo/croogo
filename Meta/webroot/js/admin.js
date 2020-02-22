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
    var spinnerClass = Admin.iconClass('spinner', false);
    aAddMeta
      .addClass('disabled')
      .prepend(Meta._spinner);
    $.get(aAddMeta.attr('href'), function (data) {
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
  $('div.meta a.remove-meta').click(function (e) {
    var aRemoveMeta = $(this);
    var spinnerClass = Admin.iconClass('spinner', false);
    var rel = parseInt(aRemoveMeta.attr('rel'), 10);
    if (rel != '' && !isNaN(rel)) {
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
            aRemoveMeta.closest('.meta').remove();
          } else {
            // error
          }
          removeSpinner();
        }
      });
    } else {
      aRemoveMeta.closest('.meta').remove();
    }

    e.preventDefault();
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
