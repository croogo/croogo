var Comments = {}

Comments.modals = function() {
	$(".comment-view").on("click", function() {
		var el= $(this)
		var modal = $('#comment-modal');
		$('#comment-modal')
			.find('.modal-header h3').html(el.data("title")).end()
			.find('.modal-body').html('<pre>' + el.data('content') + '</pre>').end()
			.modal('toggle');
		return false;
	});
}

$(function() {
	Comments.modals()
});
