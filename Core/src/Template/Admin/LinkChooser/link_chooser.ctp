<?php

$rows = [];
foreach ($linkChoosers as $name => $chooser):
    $title = $this->Html->tag('h4', $name, ['class' => 'list-group-item-heading']);
    $description = $this->Html->tag('small', $chooser['description'], ['class' => 'list-group-item-text']);
    $link = $this->Html->link($title . $description, $chooser['url'], [
        'class' => 'list-group-item',
        'escape' => false
    ]);
    $rows[] = $link;
endforeach;
?>
    <div class="list-group">
        <?php echo implode(' ', $rows); ?>
    </div>
<?php

$target = json_encode($this->request->query('target'));

$script = <<< EOF
$('.link.chooser').itemChooser({
	fields: [{ type: "Node", target: $target, attr: "rel" }]
});
EOF;

echo $this->Html->scriptBlock($script);
