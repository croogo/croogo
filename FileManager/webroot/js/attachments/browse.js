$(function() {
  $('.selector').on('click', function (e) {
    e.preventDefault();
    var slug = $(this).data('slug');

    Croogo.Wysiwyg.choose(slug);
  });
});
