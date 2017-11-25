/**
 * document ready
 *
 * @return void
 */
$(function () {
  Admin.toggleRowSelection('#MessagesCheckAll');

  $('.comment-view').on('click', function () {
    var $el = $(this);
    $('#comment-modal')
      .find('.modal-header h3').html($el.data('title')).end()
      .find('.modal-body').html('<pre>' + $el.data('content') + '</pre>').end()
      .modal('toggle');
  });
});
