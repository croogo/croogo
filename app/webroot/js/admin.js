/**
 * Admin
 *
 * for admin pages
 */
var Admin = {};

/**
 * Navigation
 *
 * @return void
 */
Admin.navigation = function() {
    //$("#navnew").accordion({header:'h3'});
	$('#navigation ul a').collapsor();

	// now find cookies and animate the li
	$("#navigation > ul > li").each(function(){
		var li_index = $("#navigation > ul > li").index(this);
		var cookie_name = "cms19_toggle_navigation_ul_li_"+li_index;
		if( $.cookie(cookie_name) ) {
			$("#navigation > ul > li:eq("+li_index+") > ul").animate({height:'toggle', opacity:'toggle'}, 500, 'swing');
		}
	});
}

/**
 * Forms
 *
 * @return void
 */
Admin.form = function() {
	$("form input[type=submit]").not('.filter input[type=submit]').addClass("ui-state-default ui-corner-all").hover(
		function(){
			$(this).addClass("ui-state-hover");
		},
		function(){
			$(this).removeClass("ui-state-hover");
		}
	)

	$("input[type=text][rel], select[rel]").not(":hidden").each(function() {
		var sd = $(this).attr('rel');
		$(this).after("<span class=\"description\">"+sd+"</span>");
	});
	$("textarea[rel]").not(":hidden").each(function() {
		var sd = $(this).attr('rel');
        if (sd != '') {
            $(this).after("<br /><span class=\"description nospace\">"+sd+"</span>");
        }
	});
}

/**
 * Extra stuff
 *
 * rounded corners, striped table rows, etc
 *
 * @return void
 */
Admin.extra = function() {
	$("table tr:nth-child(even)").not('.controller-row').addClass("striped");
	$("div.message").addClass("notice");
	$('#loading p').addClass('ui-corner-bl ui-corner-br');
}

/**
 * Rounded corners
 *
 * @return void
 */
Admin.roundedCorners = function() {
    $("#navigation ul li, #navigation ul li ul, table, .notice, .success, .error, input, select, textarea, div.actions ul li a, div.meta, div.filter").addClass('ui-corner-all');
}

/**
 * Document ready
 *
 * @return void
 */
$(document).ready(function() {
    Admin.navigation();
	Admin.form();
    Admin.extra();
    Admin.roundedCorners();

	$('.tabs').tabs();
    $('a.tooltip').tipsy({gravity: 's'});
    $('textarea').not('.file-content').elastic();
});