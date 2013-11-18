/**
 * Simple AJAX autocomplete wrapper/plugin using Bootstrap typeahead
 *
 * @license MIT
 * @author rchavik@gmail.com
 * @package Croogo
 */
;(function ($, window, document, undefined ) {

	var defaults = {

		// URL to retrieve autocomplete results
		url: undefined,

		// selector of related element that stores the actual selected value
		relatedElement: undefined,

		// field name that will be used as primary key
		primaryKey: "id",

		// field name that will be displayed in the autocomplete field
		displayField: "title",

		// field name that will be used when querying from the autocomplete URL
		queryField: undefined
	};

	var pluginName = 'typeahead_autocomplete';

	function Plugin(element, options) {
		this.element = element;

		this.options = $.extend({}, defaults, options);

		var elConfig = $(element).data();
		for (field in defaults) {
			var key = field.toLowerCase();
			if (typeof elConfig[key] !== 'undefined') {
				this.options[field] = elConfig[key];
			}
		};

		this.init(this);
	};

	Plugin.prototype = {
		init: function (plugin) {
			var options = plugin.options;
			var results = [];
			var map = {};
			var $rel = $(options.relatedElement);
			$(plugin.element).typeahead({
				matcher: function(item) {
					if (item && item.toLowerCase().indexOf(this.query.trim().toLowerCase()) !== -1) {
						return true;
					}
				},
				updater: function(item) {
					$rel.val(map[item]);
					return item;
				},
				source: function(q, process) {
					var param = {};
					param[options.queryField] = q;
					$.get(options.url, $.param(param), function (data) {
						$.each(data, function (i, result) {
							if (typeof map[result[options.displayField]] == 'undefined') {
								map[result[options.displayField]] = result[options.primaryKey];
								results.push(result[options.displayField]);
							}
						});
						return process(results);
					});
				}
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

})(jQuery, window, document)
