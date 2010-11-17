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
    $('ul.sf-menu').supersubs({
        minWidth: 12,
        maxWidth: 27,
        extraWidth: 1
    }).superfish({
        delay: 200,
        animation: {opacity:'show',height:'show'},
        speed: 'fast',
        autoArrows: true,
        dropShadows: false,
        disableHI: true
    });
}


/**
 * Forms
 *
 * @return void
 */
Admin.form = function() {
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
    $("table tr:nth-child(odd)").not('.controller-row').addClass("striped");
    $("div.message").addClass("notice");
    $('#loading p').addClass('ui-corner-bl ui-corner-br');
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

    $('.tabs').tabs();
    $('a.tooltip').tipsy({gravity: 's', html: false});
    $('textarea').not('.content').elastic();
});
