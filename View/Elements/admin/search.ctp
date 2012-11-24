<?php
if (empty($modelClass)) {
	$modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
	$className = strtolower($this->name);
}
if (!empty($searchFields)):
?>

<div class="<?php echo $className; ?> filter row-fluid">
<?php
	echo $this->Form->create($modelClass, array(
		'class' => 'form-inline',
		'url' => array('action' => 'index')
	));
	$options = array(
		'empty' => '',
	);
	foreach ($searchFields as $field) {
		$this->Form->unlockField($field);
		echo $this->Form->input($field, $options);
	}

	echo $this->Form->submit(__('Filter'), array('div' => 'input submit'));
	echo $this->Form->end();
?>
</div>
<?php endif; ?>
