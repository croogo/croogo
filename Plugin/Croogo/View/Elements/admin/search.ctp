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
		'novalidate' => true,
		'url' => array(
			'plugin' => $this->request->params['plugin'],
			'controller' => $this->request->params['controller'],
			'action' => $this->request->params['action'],
		),
	));
	echo $this->Form->input('chooser', array(
		'type' => 'hidden',
		'value' => isset($this->request->query['chooser']),
	));
	foreach ($searchFields as $field => $fieldOptions) {
		$options = array('empty' => '', 'required' => false);
		if (is_numeric($field) && is_string($fieldOptions)) {
			$field = $fieldOptions;
			$fieldOptions = array();
		}
		if (!empty($fieldOptions)) {
			$options = Hash::merge($fieldOptions, $options);
		}
		$this->Form->unlockField($field);
		echo $this->Form->input($field, $options);
	}

	echo $this->Form->submit(__d('croogo', 'Filter'), array('div' => 'input submit'));
	echo $this->Form->end();
?>
</div>
<?php endif; ?>
