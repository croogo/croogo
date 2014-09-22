<?php $domId = Inflector::slug($alias, '-'); ?>
<div id="<?php echo $domId; ?>" class="blogfeed"></div>
<?php

$script =<<<EOF
$.get("http://blog.croogo.org/promoted.rss", function(data) {
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
	$('#$domId').html(buffer);
});
EOF;

$this->Js->buffer($script);
