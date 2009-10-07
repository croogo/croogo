/**
 * Pages
 *
 * for PagesController
 */
var Pages = {};

/**
 * functions to execute when document is ready
 *
 * only for PagesController
 *
 * @return void
 */
Pages.documentReady = function() {
    Pages.addMeta();
    Pages.removeMeta();
}

/**
 * add meta field
 *
 * @return void
 */
Pages.addMeta = function() {
    $('a.add-meta').click(function() {
        $.get(baseUrl+'admin/pages/add_meta/', function(data) {
            $('#meta-fields div.clear').before(data);
            $('div.meta a.remove-meta').unbind();
            Pages.removeMeta();
        });
        return false;
    });
}

/**
 * remove meta field
 *
 * @return void
 */
Pages.removeMeta = function() {
    $('div.meta a.remove-meta').click(function() {
        var aRemoveMeta = $(this);
        if (aRemoveMeta.attr('rel') != '') {
            $.getJSON(baseUrl+'admin/pages/delete_meta/'+$(this).attr('rel')+'.json', function(data) {
                if (data.success) {
                    aRemoveMeta.parents('.meta').remove();
                } else {
                    // error
                }
            });
        } else {
            aRemoveMeta.parents('.meta').remove();
        }
        return false;
    });
}

/**
 * document ready
 *
 * @return void
 */
$(document).ready(function() {
    if (params.controller == 'pages') {
        Pages.documentReady();
    }
});