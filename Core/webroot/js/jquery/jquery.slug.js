//
//  jQuery Slug Plugin by Perry Trinier (perrytrinier@gmail.com)
//  MIT License: http://www.opensource.org/licenses/mit-license.php
//@TODO: This should rather load the slug using AJAX to ensure that an accurate slug is generated.
;(
  function ($) {
    function transliterate(str) {
      var rExps = [
        {
          re: /ä|æ|ǽ/g, ch: 'ae'
        }, {
          re: /ö|œ/g, ch: 'oe'
        }, {
          re: /ü/g, ch: 'ue'
        }, {
          re: /Ä/g, ch: 'Ae'
        }, {
          re: /Ü/g, ch: 'Ue'
        }, {
          re: /Ö/g, ch: 'Oe'
        }, {
          re: /À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ/g, ch: 'A'
        }, {
          re: /à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/g, ch: 'a'
        }, {
          re: /Ç|Ć|Ĉ|Ċ|Č/g, ch: 'C'
        }, {
          re: /ç|ć|ĉ|ċ|č/g, ch: 'c'
        }, {
          re: /Ð|Ď|Đ/g, ch: 'D'
        }, {
          re: /ð|ď|đ/g, ch: 'd'
        }, {
          re: /È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/g, ch: 'E'
        }, {
          re: /è|é|ê|ë|ē|ĕ|ė|ę|ě/g, ch: 'e'
        }, {
          re: /Ĝ|Ğ|Ġ|Ģ/g, ch: 'G'
        }, {
          re: /ĝ|ğ|ġ|ģ/g, ch: 'g'
        }, {
          re: /Ĥ|Ħ/g, ch: 'H'
        }, {
          re: /ĥ|ħ/g, ch: 'h'
        }, {
          re: /Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ/g, ch: 'I'
        }, {
          re: /ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı/g, ch: 'i'
        }, {
          re: /Ĵ/g, ch: 'J'
        }, {
          re: /ĵ/g, ch: 'j'
        }, {
          re: /Ķ/g, ch: 'K'
        }, {
          re: /ķ/g, ch: 'k'
        }, {
          re: /Ĺ|Ļ|Ľ|Ŀ|Ł/g, ch: 'L'
        }, {
          re: /ĺ|ļ|ľ|ŀ|ł/g, ch: 'l'
        }, {
          re: /Ñ|Ń|Ņ|Ň/g, ch: 'N'
        }, {
          re: /ñ|ń|ņ|ň|ŉ/g, ch: 'n'
        }, {
          re: /Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/g, ch: 'O'
        }, {
          re: /ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/g, ch: 'o'
        }, {
          re: /Ŕ|Ŗ|Ř/g, ch: 'R'
        }, {
          re: /ŕ|ŗ|ř/g, ch: 'r'
        }, {
          re: /Ś|Ŝ|Ş|Š/g, ch: 'S'
        }, {
          re: /ś|ŝ|ş|š|ſ/g, ch: 's'
        }, {
          re: /Ţ|Ť|Ŧ/g, ch: 'T'
        }, {
          re: /ţ|ť|ŧ/g, ch: 't'
        }, {
          re: /Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/g, ch: 'U'
        }, {
          re: /ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/g, ch: 'u'
        }, {
          re: /Ý|Ÿ|Ŷ/g, ch: 'Y'
        }, {
          re: /ý|ÿ|ŷ/g, ch: 'y'
        }, {
          re: /Ŵ/g, ch: 'W'
        }, {
          re: /ŵ/g, ch: 'w'
        }, {
          re: /Ź|Ż|Ž/g, ch: 'Z'
        }, {
          re: /ź|ż|ž/g, ch: 'z'
        }, {
          re: /Æ|Ǽ/g, ch: 'AE'
        }, {
          re: /ß/g, ch: 'ss'
        }, {
          re: /Ĳ/g, ch: 'IJ'
        }, {
          re: /ĳ/g, ch: 'ij'
        }, {
          re: /Œ/g, ch: 'OE'
        }, {
          re: /ƒ/g, ch: 'f'
        }, // Cyrillic Letters
        {
          re: /А/g, ch: 'A'
        }, {
          re: /Б/g, ch: 'B'
        }, {
          re: /В/g, ch: 'V'
        }, {
          re: /Г/g, ch: 'G'
        }, {
          re: /Д/g, ch: 'D'
        }, {
          re: /Е/g, ch: 'E'
        }, {
          re: /Ё/g, ch: 'YO'
        }, {
          re: /Ж/g, ch: 'ZH'
        }, {
          re: /З/g, ch: 'Z'
        }, {
          re: /И/g, ch: 'I'
        }, {
          re: /Й/g, ch: 'Y'
        }, {
          re: /К/g, ch: 'K'
        }, {
          re: /Л/g, ch: 'L'
        }, {
          re: /М/g, ch: 'M'
        }, {
          re: /Н/g, ch: 'N'
        }, {
          re: /О/g, ch: 'O'
        }, {
          re: /П/g, ch: 'P'
        }, {
          re: /Р/g, ch: 'R'
        }, {
          re: /С/g, ch: 'S'
        }, {
          re: /Т/g, ch: 'T'
        }, {
          re: /У/g, ch: 'U'
        }, {
          re: /Ф/g, ch: 'F'
        }, {
          re: /Х/g, ch: 'H'
        }, {
          re: /Ц/g, ch: 'TS'
        }, {
          re: /Ч/g, ch: 'CH'
        }, {
          re: /Ш/g, ch: 'SH'
        }, {
          re: /Щ/g, ch: 'SH'
        }, {
          re: /Ъ/g, ch: ''
        }, {
          re: /Ы/g, ch: 'Y'
        }, {
          re: /Ь/g, ch: ''
        }, {
          re: /Э/g, ch: 'E'
        }, {
          re: /Ю/g, ch: 'YU'
        }, {
          re: /Я/g, ch: 'YA'
        }, {
          re: /а/g, ch: 'a'
        }, {
          re: /б/g, ch: 'b'
        }, {
          re: /в/g, ch: 'v'
        }, {
          re: /г/g, ch: 'g'
        }, {
          re: /д/g, ch: 'd'
        }, {
          re: /е/g, ch: 'e'
        }, {
          re: /ё/g, ch: 'yo'
        }, {
          re: /ж/g, ch: 'zh'
        }, {
          re: /з/g, ch: 'z'
        }, {
          re: /и/g, ch: 'i'
        }, {
          re: /й/g, ch: 'y'
        }, {
          re: /к/g, ch: 'k'
        }, {
          re: /л/g, ch: 'l'
        }, {
          re: /м/g, ch: 'm'
        }, {
          re: /н/g, ch: 'n'
        }, {
          re: /о/g, ch: 'o'
        }, {
          re: /п/g, ch: 'p'
        }, {
          re: /р/g, ch: 'r'
        }, {
          re: /с/g, ch: 's'
        }, {
          re: /т/g, ch: 't'
        }, {
          re: /у/g, ch: 'u'
        }, {
          re: /ф/g, ch: 'f'
        }, {
          re: /х/g, ch: 'h'
        }, {
          re: /ц/g, ch: 'ts'
        }, {
          re: /ч/g, ch: 'ch'
        }, {
          re: /ш/g, ch: 'sh'
        }, {
          re: /щ/g, ch: 'sh'
        }, {
          re: /ъ/g, ch: ''
        }, {
          re: /ы/g, ch: 'y'
        }, {
          re: /ь/g, ch: ''
        }, {
          re: /э/g, ch: 'e'
        }, {
          re: /ю/g, ch: 'yu'
        }, {
          re: /я/g, ch: 'ya'
        }
      ];
      for (var i = 0, len = rExps.length; i < len; i++) {
        str = str.replace(rExps[i].re, rExps[i].ch);
      }
      return str;
    };

    function makeSlug(input) {
      var slug = transliterate(jQuery.trim(input))
        .replace(/\s+/g, '-').replace(/[^a-zA-Z0-9\-]/g, '').toLowerCase()
        .replace(/\-{2,}/g, '-')
        .replace(/\-$/, '')
        .replace(/^\-/, '');
      return slug;
    };

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
