<?php
if (empty($modelClass)) {
	$modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
	$className = strtolower($this->name);
}
if (!empty($searchFields)):
?>

<div class="<?php echo $className; ?> filter form">
<?php
	echo $this->Form->create($modelClass, array(
		'url' => array_merge(array('action' => 'index'), $this->params['pass'])
	));
	$options = array('empty' => '');
	foreach ($searchFields as $field) {
		$this->Form->unlockField($field);
		echo $this->Form->input($field, $options);
	}

	echo $this->Form->end(__('Filter'));
?>
<div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
