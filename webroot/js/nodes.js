/**
 * Nodes
 *
 * for NodesController
 */
var Nodes = {};

/**
 * functions to execute when document is ready
 *
 * only for NodesController
 *
 * @return void
 */
Nodes.documentReady = function() {
    Nodes.filter();
    Nodes.addMeta();
    Nodes.removeMeta();
}

/**
 * Submits form for filtering Nodes
 *
 * @return void
 */
Nodes.filter = function() {
    if (typeof(window['filter']) == 'undefined') {
        $('.nodes div.filter').hide();
    }

    $('.nodes div.actions a.filter').click(function() {
        $('.nodes div.filter').slideToggle();
        return false;
    });

    $('#FilterAddForm div.submit input').click(function() {
        $('#FilterAddForm').submit();
        return false;
    });

    $('#FilterAdminIndexForm').submit(function() {
        var filter = '';

        // type
        if ($('#FilterType').val() != '') {
            filter += 'type:' + $('#FilterType').val() + ';';
        }

        // status
        if ($('#FilterStatus').val() != '') {
            filter += 'status:' + $('#FilterStatus').val() + ';';
        }

        // promoted
        if ($('#FilterPromote').val() != '') {
            filter += 'promote:' + $('#FilterPromote').val() + ';';
        }

        var loadUrl = Croogo.basePath + 'admin/nodes/index/';
        if (filter != '') {
            loadUrl += 'filter:' + filter;
        }

        window.location = loadUrl;
        return false;
    });
}

/**
 * add meta field
 *
 * @return void
 */
Nodes.addMeta = function() {
    $('a.add-meta').click(function() {
        $.get(Croogo.basePath+'admin/nodes/add_meta/', function(data) {
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
            $.getJSON(Croogo.basePath+'admin/nodes/delete_meta/'+$(this).attr('rel')+'.json', function(data) {
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
 * Create slugs based on title field
 *
 * @return void
 */
Nodes.slug = function() {
    $("#NodeTitle").slug({
        slug:'slug',
        hide: false
    });
}

/**
 * document ready
 *
 * @return void
 */
$(document).ready(function() {
    if (Croogo.params.controller == 'nodes') {
        Nodes.documentReady();
        if (Croogo.params.action == 'admin_add') {
            Nodes.slug();
        }
    }
});