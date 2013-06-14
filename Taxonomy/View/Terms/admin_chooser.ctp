
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
	
	// Default Content Type
	if(isset($vocabulary['Type'][0])){
		$defaultType = $vocabulary['Type'][0];
	}
	if(isset($this->params->query['type_id'])){
		if(isset($vocabulary['Type'][$this->params->query['type_id']])){
			$defaultType = $vocabulary['Type'][$this->params->query['type_id']];
		}
	}
	

	foreach ($termsTree as $id => $title):

		// Title Column
		$titleCol = $title;
		if(isset($defaultType['alias'])){
			$titleCol = $this->Html->link($title,array(
			'plugin'=>'nodes',
			'controller'=>'nodes',
			'action'=>'term',
			'type'=>$defaultType['alias'],
			'slug'=>$terms[$id]['slug'],
			'admin'=>0
			),array(
				'class' => 'item-choose',
				'data-chooser_type' => 'Node',
				'data-chooser_id' => $id,
				'rel' => sprintf(
					'plugin:%s/controller:%s/action:%s/type:%s/slug:%s',
					'nodes',
					'nodes',
					'term',
					$defaultType['alias'],
					$terms[$id]['slug']
					),
			));
		}

		$rows[] = array(
			'',
			$id,
			$titleCol,
			$terms[$id]['slug'],
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
