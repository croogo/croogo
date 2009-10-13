/**
 * Nodes
 *
 * for NodesController
 */
var Nodes = {};

/**
 * functions to execute when document is ready
 *
 * only for PagesController
 *
 * @return void
 */
Nodes.documentReady = function() {
    Nodes.addMeta();
    Nodes.removeMeta();
}

/**
 * add meta field
 *
 * @return void
 */
Nodes.addMeta = function() {
    $('a.add-meta').click(function() {
        $.get(baseUrl+'admin/nodes/add_meta/', function(data) {
            $('#meta-fields div.clear').before(data);
            $('div.meta a.remove-meta').unbind();
            Admin.roundedCorners();
            Nodes.removeMeta();
        });
        return false;
    });
}

/**
 * remove meta field
 *
 * @return void
 */
Nodes.removeMeta = function() {
    $('div.meta a.remove-meta').click(function() {
        var aRemoveMeta = $(this);
        if (aRemoveMeta.attr('rel') != '') {
            $.getJSON(baseUrl+'admin/nodes/delete_meta/'+$(this).attr('rel')+'.json', function(data) {
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
    if (params.controller == 'nodes') {
        Nodes.documentReady();
    }
});