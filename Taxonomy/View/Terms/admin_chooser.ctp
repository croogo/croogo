<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		'',
		__d('croogo', 'Id'),
		__d('croogo', 'Title'),
		__d('croogo', 'Slug'),
	));
?>
<thead>
	<?php echo $tableHeaders; ?>
</thead>
<?php
	$rows = array();

	foreach ($terms as $term):
		$titleCol = $term['Term']['title'];
		if (isset($defaultType)) {
			$titleCol = $this->Html->link($term['Term']['title'], array(
				'plugin' => 'nodes',
				'controller' => 'nodes',
				'action' => 'term',
				'type' => $defaultType['alias'],
				'slug' => $term['Term']['slug'],
				'admin' => false
			), array(
				'class' => 'item-choose',
				'data-chooser_type' => 'Node',
				'data-chooser_id' => $term['Term']['id'],
				'rel' => sprintf(
					'plugin:%s/controller:%s/action:%s/type:%s/slug:%s',
					'nodes',
					'nodes',
					'term',
					$defaultType['alias'],
					$term['Term']['slug']
				),
			));
		}

		$rows[] = array(
			'',
			$term['Term']['id'],
			$titleCol,
			$term['Term']['slug'],
		);

	endforeach;

	echo $this->Html->tableCells($rows);

?>
</table>
<?php

$script =<<<EOF
$('.popovers').popover().on('click', function() { return false; });;
EOF;
$this->Js->buffer($script);
