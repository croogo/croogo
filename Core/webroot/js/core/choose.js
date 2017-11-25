;(
  function ($, window, document, undefined) {

    var pluginName = 'itemChooser';

    var defaults = {
      itemSelector: '.item-choose'
    };

    function Plugin(element, options) {
      this.element = $(element);
      this.modal = $(this.element.data('target'));
      this.target = $(this.element.data('chooserTarget'));

      this.options = $.extend({}, defaults, options);

      this._defaults = defaults;
      this._name = pluginName;

      this.init();
    };

    Plugin.prototype = {

      init: function () {
        this.element.on('click', $.proxy(this.loadChooser, this));
      },

      clickCallback: function (e, data) {
        this.modal.modal('hide');
        var $target = $(e.target);
        var attr = $target.data('chooserAttr');
        var type = $target.data('chooserType');
        if (type !== $(data).data('chooserType')) {
          return;
        }
        $target.val($(data).attr(attr));
      },

      loadChooser: function (e) {
        e.preventDefault();
        var plugin = this;
        var options = plugin.options;
        var events = 'chooserSelect';
        var $link = $(e.target);

        plugin.target.data('chooserAttr', plugin.element.data('attr'));
        plugin.target.data('chooserType', plugin.element.data('type'));
        if (plugin.target.data('chooserAttached') !== true) {
          if (typeof options.callback == 'function') {
            plugin.target.on(events, $.proxy(options.callback, plugin));
          } else {
            plugin.target.on(events, $.proxy(plugin.clickCallback, plugin));
          }
          plugin.target.data('chooserAttached', true);
        }

        plugin.modal
          .find('.modal-title').html($link.data('title')).end()
          .find('.modal-body').html('Loading...');
        $.ajax({
          url: $link.attr('href'),
          datatype: 'html'
        })
          .done(function (response) {
            var $response = $(response)
            plugin.modal.find('.modal-body').html($response);
            $response.on('click', options.itemSelector, function (e) {
              e.preventDefault();
              plugin.target.trigger('chooserSelect', this);
            })
          });
      }

    };

    $.fn[pluginName] = function (options) {
      return this.each(function () {
        if (!$.data(this, 'plugin_' + pluginName)) {
          $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
        }
      });
    };

    $(function() {
      $('[data-chooser]')[pluginName]();
    });

  }
)(jQuery, window, document)
