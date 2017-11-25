$(function () {
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
});
