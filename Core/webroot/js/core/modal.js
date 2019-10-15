var Admin = typeof Admin == 'undefined' ? {} : Admin;

Admin.modal = function() {
  $(document).on('show.bs.modal', function (e) {
    var $button = $(e.relatedTarget);
    var $modal = $(e.target);

    if (!$button.data('remote')) {
      return;
    }

    var remote = $button.data('remote');
    $modal.find('.modal-body')
      .load(remote, function () {
        $modal.data('bs.modal').handleUpdate();
      });
  });

  $('body').on('click', '.modal-dialog a:not(.item-choose,.popovers)', function(event) {
    var $el = $(event.currentTarget);
    var href = $el.attr('href');
    if (href) {
      $el.closest('.modal-body').load(href);
    }
    event.preventDefault();
    return false;
  });

  $('body').on('submit', '.modal-dialog form', function(event) {
    var $el = $(event.target);
    var $form = $el.closest('form');
    $form.submit(function(ev) {
      ev.preventDefault();
      var opts = {
        type: 'POST',
        url: $form.attr('action'),
        data: $form.serialize(),
        success: function(data) {
          $el.closest('.modal-body').html(data);
        }
      };
      $.ajax(opts);
    });
    event.preventDefault();
    return false;
  });
};
