/**
 * Submits form for filtering Nodes
 *
 * @return void
 */
function filter () {
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
		var q='';
		
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
        
        //query string
        if($('#FilterQ').val() != '') {
            q=$('#FilterQ').val();
        }
        var loadUrl = '/admin/nodes/index/links:1/';
        if (filter != '') {
            loadUrl += 'filter:' + filter;
        }
        if (q != '') {
            if (filter == '') {
                loadUrl +='q:'+q;
            } else {
                loadUrl +='/q:'+q;
            }
        }
        
        window.location = loadUrl;
        return false;
    });
}
$(document).ready(function() {
	filter();
});
