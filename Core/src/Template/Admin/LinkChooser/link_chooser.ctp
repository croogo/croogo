<?php

$rows = array();
foreach ($linkChoosers as $name => $chooser):
	$link = $this->CroogoHtml->link('', $chooser['url'], array(
		'icon' => $this->Theme->getIcon('search'),
		'iconSize' => 'small',
		'button' => array('default', 'small'),
		'class' => 'link chooser pull-right',
		'data-dismiss' => 'modal',
		'tooltip' => array(
			'data-title' => __d('croogo', 'Link to %s', $name),
			'data-placement' => 'left',
		),
	));
	$title = $this->CroogoHtml->tag('h5', $name . $link);
	$div = $this->CroogoHtml->div('link_chooser', $title . $this->CroogoHtml->tag('small', $chooser['description']));
	$rows[] = '<tr><td>' . $div . '</td></tr>';
endforeach;
?>
<table class="table table-striped">
	<?php echo implode(' ', $rows); ?>
</table>
<?php

$target = json_encode($this->request->query('target'));

$script =<<< EOF
$('.link.chooser').itemChooser({
	fields: [{ type: "Node", target: $target, attr: "rel" }]
});
$(".link_chooser a").click(function() {
	$("#link_choosers").modal('hide');
});
EOF;

echo $this->Html->scriptBlock($script);
