$(function(){
	/*$("#link_choosers").dialog({
	autoOpen:false,
	width:600,
	height:400
	});*/

	$("#link_choosers .btn").click(function(){
		$("#link_choosers").dialog('close');
	});

	$("a.launch_link_choosers").click(function(){
		$("#link_choosers").dialog('open');
	});
});
