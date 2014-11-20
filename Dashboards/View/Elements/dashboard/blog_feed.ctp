<div class="blogfeed">
	<?php echo $this->Html->icon(array('spin', 'spinner')); ?>
</div>
<?php

$script =<<<EOF
var feedUrl = "https://blog.croogo.org/promoted.rss";
$.get(feedUrl, { utm_source: "admin-dashboard" }, function(data) {
	var xml = $(data);
	var buffer = '';
	var _title = _.template(
		'<h1>' +
		'<a target=_blank href="<%= link %>"><%= title %></a>' +
		'</h1>'
	);
	xml.find("item").each(function() {
		var This = $(this);
		var item = {
			title: This.find("title").text(),
			link: This.find("link").text(),
			description: This.find("description").text(),
			pubDate: This.find("pubDate").text(),
			author: This.find("author").text()
		};
		buffer += _title(item);
		buffer += '<small>' + item.pubDate + '</small>';
	});
	$('#$alias .blogfeed').html(buffer);
});
EOF;

$this->Js->buffer($script);
