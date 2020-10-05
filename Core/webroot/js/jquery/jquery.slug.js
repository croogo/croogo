//
//  jQuery Slug Plugin by Perry Trinier (perrytrinier@gmail.com)
//  MIT License: http://www.opensource.org/licenses/mit-license.php
//@TODO: This should rather load the slug using AJAX to ensure that an accurate slug is generated.
;(
  function ($) {
    function makeSlug(input) {
      var slug = transliterate(jQuery.trim(input))
        .replace(/\s+/g, '-').replace(/[^a-zA-Z0-9\-]/g, '').toLowerCase()
        .replace(/\-{2,}/g, '-')
        .replace(/\-$/, '')
        .replace(/^\-/, '');
      return slug;
    }
    $.fn.slug = function (options) {
      var settings = {
        selector: '',
        slugClass: 'slug',
        hide: true,
        editable: true,
        editLabel: 'Edit',
        editClass: 'btn btn-secondary btn-sm'
      };

      if (options) {
        $.extend(settings, options);
      }

      return this.each(function () {
        var $target = $(this);
        var $slugInput = $($target.data('slug') ? $target.data('slug') : settings.selector);
        var $slugSpan = $('<span class="slug">&nbsp;</span>')
          .addClass($target.data('slugClass') ? $target.data('slugClass') : settings.slugClass);
        var $slugEdit = $('<a href="#" class="editable"></a>')
          .addClass($target.data('slugEditClass') ? $target.data('slugEditClass') : settings.editClass)
          .html($target.data('slugEditLabel') ? $target.data('slugEditLabel') : settings.editLabel);

        if ($target.data('slugEditLabel')) {
          $slugEdit.hide();
        }

        if (settings.hide || $target.data('slugHide')) {
          $slugInput
            .after($slugSpan)
            .hide();
        }
        if (settings.editable || $target.data('slugEditable')) {
          $slugSpan.after($slugEdit);
        }

        if ($slugInput.val()) {
          $slugSpan.text($slugInput.val());
          if (settings.editable) {
            $slugEdit.show();
          }
        }

        $target.on('keyup.slugger', function() {
          var slug = makeSlug($target.val());

          $slugInput.val(slug);
          $slugSpan.text(slug);

          if (settings.editable && slug) {
            $slugEdit.show();
          } else {
            if ($target.data('slugEditLabel')) {
              $slugEdit.hide();
            }
          }
        });

        $slugEdit.on('click.slugger', function(e) {
          e.preventDefault();
          $slugEdit.remove();
          $slugSpan.remove();
          $slugInput.show();
        });
      });
    };
  }(jQuery)
);

jQuery(function ($) {
  $(':input[data-slug]').slug();
});
