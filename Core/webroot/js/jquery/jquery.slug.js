//
//  jQuery Slug Plugin by Perry Trinier (perrytrinier@gmail.com)
//  MIT License: http://www.opensource.org/licenses/mit-license.php
//@TODO: This should rather load the slug using AJAX to ensure that an accurate slug is generated.
;(
  function ($) {
    function transliterate(str) {
      if (str == null) return '';

      // 1) translit cyrylicy (ru/uk/bg itp.)
      const cyr = {
        'А':'A','Б':'B','В':'V','Г':'G','Д':'D','Е':'E','Ё':'YO','Ж':'ZH','З':'Z','И':'I','Й':'Y','К':'K','Л':'L','М':'M',
        'Н':'N','О':'O','П':'P','Р':'R','С':'S','Т':'T','У':'U','Ф':'F','Х':'H','Ц':'TS','Ч':'CH','Ш':'SH','Щ':'SHCH','Ъ':'',
        'Ы':'Y','Ь':'','Э':'E','Ю':'YU','Я':'YA',
        'а':'a','б':'b','в':'v','г':'g','д':'d','е':'e','ё':'yo','ж':'zh','з':'z','и':'i','й':'y','к':'k','л':'l','м':'m',
        'н':'n','о':'o','п':'p','р':'r','с':'s','т':'t','у':'u','ф':'f','х':'h','ц':'ts','ч':'ch','ш':'sh','щ':'shch','ъ':'',
        'ы':'y','ь':'','э':'e','ю':'yu','я':'ya'
      };
      str = String(str).replace(/[\u0400-\u04FF]/g, ch => cyr[ch] != null ? cyr[ch] : ch);

      // 2) szczególne ligatury/znaki z latin extended
      const specials = {
        'ß':'ss','Æ':'AE','æ':'ae','Ø':'OE','ø':'oe','Å':'AA','å':'aa','Ð':'D','ð':'d','Þ':'Th','þ':'th',
        'Ł':'L','ł':'l','Œ':'OE','œ':'oe'
      };
      str = str.replace(/[^\u0000-\u007E]/g, ch => specials[ch] != null ? specials[ch] : ch);

      // 3) usunięcie diakrytyków (á → a, Ć → C, Ǽ → AE już ogarnięte wyżej)
      // Uwaga: działa od Chrome 54+/Edge 79+/FF 31+; jeśli potrzebujesz IE11, trzeba polyfill.
      str = str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');

      return str;
    }
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
