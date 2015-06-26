;(function ($, window, document, undefined ) {

	var pluginName = 'itemChooser';

	var defaults = {
		// selector that triggers chooser_select event
		itemSelector: 'a.item-choose',

		// field configuration:
		// eg:
		//	{ type: "Node", target: "id", attr: "data-id" },
		//	{ type: "Block", target: "title", attr: "data-title" }
		fields: []
	};

	function Plugin(element, options) {
		this.element = element;

		this.options = $.extend({}, defaults, options);

		this._defaults = defaults;
		this._name = pluginName;

		this.init();
	};

	Plugin.prototype = {

		init: function() {
			$(this.element).on('click', this.openThickbox);
		},

		clickCallback: function(e, data) {
			var $this = $(this);
			var attr = $this.data('chooserAttr');
			var type = $this.data('chooserType');
			if (type != $(data).attr('data-chooser_type')) {
				return;
			}
			$this.val($(data).attr(attr));
			tb_remove();
		},

		openThickbox: function(e) {
			var $this = $(this);
			var $plugin = $this.data('plugin_' + pluginName);
			var options = $plugin.options;

			for (var i in options.fields) {
				var config = options.fields[i];
				var $el = $(config.target);
				var attr = config.attr;
				var events = 'chooser_select';
				$el.data('chooserAttr', config.attr);
				$el.data('chooserType', config.type);
				if ($el.data('chooserAttached') === true) {
					continue;
				}
				if (typeof config.callback == 'function') {
					$el.on(events, config.callback);
				} else {
					$el.on(events, $plugin.clickCallback);
				}
				$el.data('chooserAttached', true);
			};

			tb_show(null, $this.attr('href'));

			var $iframe = $('#TB_iframeContent').on('load', function() {
				$iframe.contents().on('click', options.itemSelector, function (e) {
					parent.$('body *').trigger('chooser_select', this);
					return false;
				});
			});

			return false;
		}

	};

	$.fn[pluginName] = function(options) {
		return this.each(function() {
			if (!$.data(this, 'plugin_' + pluginName)) {
				$.data(this, 'plugin_' + pluginName, new Plugin(this, options));
			}
		});
	};

})(jQuery, window, document)
