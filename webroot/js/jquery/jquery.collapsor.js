/*
* collapsor (1.0) // 2008.04.05 // <http://plugins.jquery.com/project/collapsor>
* 
* REQUIRES jQuery 1.2.3+ <http://jquery.com/>
* 
* Copyright (c) 2008 TrafficBroker <http://www.trafficbroker.co.uk>
* Licensed under GPL and MIT licenses
* 
* collapsor opens and closes sublevel elements, like a collapsable menu
*
* We need to select the clickable elements that trigger the opening action of the sublevels: $('#menu ul li a').collapsor();
* The sublevel element must be in the same level than the triggers
*
* Sample Configuration:
* $('ul a').collapsor();
* 
* Config Options:
* activeClass: Class added to the element when is active // Default: 'active'
* openClass: Class added to the element when is open // Default: 'open'
* sublevelElement: Element that must open or close // Default: 'ul'
* speed: Speed for the opening animation // Default: 500
* easing: Easing for the opening animation. Other than 'swing' or 'linear' must be provided by plugin // Default: 'swing'
* 
* We can override the defaults with:
* $.fn.collapsor.defaults.speed = 1000;
* 
* @param  settings  An object with configuration options
* @author    Jesus Carrera <jesus.carrera@trafficbroker.co.uk>
*/
(function($) {
$.fn.collapsor = function(settings) { 
	// override default settings
	settings = $.extend({}, $.fn.collapsor.defaults, settings);
	var triggers = this;
	// for each element
	return this.each(function() {
		// occult the collapsing elements
		$(this).find('+ ' + settings.sublevelElement).hide();
		//show the opened
		if($(this).hasClass(settings.openClass)){
			$(this).find('+ ' + settings.sublevelElement).show();
		}
		
		// event handling
	  $(this).click(function() {
			// remove the active class from all the elements less the clicked
			$(triggers).not($(this)).removeClass(settings.openClass);
			
			// if the new active have sublevels
			if ($(this).next().is(settings.sublevelElement)){
				// blur and add the active class to the clicked
				$(this).blur().toggleClass(settings.openClass);
				// toggle the clicked
				$(this).next().animate({height:'toggle', opacity:'toggle'}, settings.speed, settings.easing);
				
					// some jquery by fahad19
					var li = $(this).parent(); 
					var li_index = $("#navigation > ul > li").index(li);
					
					var cookie_prefix = "cms19_toggle_navigation_ul_li_";
					var cookie_options = { path: '/', expires: 10 };
					var cookie_name = cookie_prefix+li_index;
					
					if ($.cookie(cookie_name)) {
						$.cookie(cookie_name, null, cookie_options);
					} else {
						$.cookie(cookie_name, '1', cookie_options);
					}
					//$("#navigation > ul > li:eq("+li_index+")").css('background', '#fff');

				
				// hide the rest
				//$(this).parent().parent().find(settings.sublevelElement).not($(this).next()).animate({height:'hide', opacity:'hide'}, settings.speed, settings.easing);
				return false;
			}
	   });
	});
};
// default settings
$.fn.collapsor.defaults = {
	activeClass: 'active',
	openClass:'open',
	sublevelElement: 'ul',
	speed: 500,
	easing: 'swing'
};
})(jQuery);