<?php
	if (isset($this->params['named']['filter'])) {
		$this->Html->scriptBlock('var filter = 1;', array('inline' => false));
	}
?>
<div class="filter">
<?php
	echo $this->Form->create('Filter');
	$filterType = '';
	if (isset($filters['type'])) {
		$filterType = $filters['type'];
	}
	$types = Set::sort($types, '{n}.Type.title', 'asc');
	$types = Set::combine($types, '{n}.Type.alias', '{n}.Type.title');
	echo $this->Form->input('Filter.type', array(
		'options' => $types,
		'empty' => true,
		'value' => $filterType,
	));
	$filterStatus = '';
	if (isset($filters['status'])) {
		$filterStatus = $filters['status'];
	}
	echo $this->Form->input('Filter.status', array(
		'options' => array(
			'1' => __('Published'),
			'0' => __('Unpublished'),
		),
		'empty' => true,
		'value' => $filterStatus,
	));
	$filterPromote = '';
	if (isset($filters['promote'])) {
		$filterPromote = $filters['promote'];
	}
	echo $this->Form->input('Filter.promote', array(
		'label' => __('Promoted'),
		'options' => array(
			'1' => __('Yes'),
			'0' => __('No'),
		),
		'empty' => true,
		'value' => $filterPromote,
	));

	$filterSearch = '';
	if (isset($this->params['named']['q'])) {
		$filterSearch = $this->params['named']['q'];
	}
	echo $this->Form->input('Filter.q', array(
		'label' => __('Search'),
		'value' => $filterSearch,
	));
	echo $this->Form->end(__('Filter'));
?>
	<div class="clear">&nbsp;</div>
</div>