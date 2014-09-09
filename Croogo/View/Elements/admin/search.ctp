<?php
if (empty($modelClass)) {
	$modelClass = Inflector::singularize($this->name);
}
if (!isset($className)) {
	$className = strtolower($this->name);
}
if (!empty($searchFields)):
?>
<div class="<?php echo $className; ?> filter">
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
	if (isset($this->request->query['chooser'])):
		echo $this->Form->input('chooser', array(
			'type' => 'hidden',
			'value' => isset($this->request->query['chooser']),
		));
	endif;
	foreach ($searchFields as $field => $fieldOptions) {
		$options = array('empty' => '', 'required' => false);
		if (is_numeric($field) && is_string($fieldOptions)) {
			$field = $fieldOptions;
			$fieldOptions = array();
		}
		if (!empty($fieldOptions)) {
			$options = Hash::merge($fieldOptions, $options);
		}
		$label = $field;
		if (substr($label, -3) === '_id') {
			$label = substr($label, 0, -3);
		}
		$label = __(Inflector::humanize(Inflector::underscore($label)));
		$options['label'] = __d('croogo', $label);
		$this->Form->unlockField($field);
		echo $this->Form->input($field, $options);
	}

	echo $this->Form->submit(__d('croogo', 'Filter'), array('div' => false));
	echo $this->Form->end();
?>
</div>
<?php endif; ?>
