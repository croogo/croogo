<?php
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

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
	echo $this->CroogoForm->create($modelClass, [
		'class' => 'form-inline',
		'novalidate' => true,
		'url' => [
			'plugin' => $this->request->params['plugin'],
			'controller' => $this->request->params['controller'],
			'action' => $this->request->params['action'],
		],
	]);
	$this->CroogoForm->templates([
		'submitContainer' => '<div class="input submit">{{content}}</div>'
	]);
	echo $this->CroogoForm->input('chooser', [
		'type' => 'hidden',
		'value' => isset($this->request->query['chooser']),
	]);
	foreach ($searchFields as $field => $fieldOptions) {
		$options = ['empty' => '', 'required' => false];
		if (is_numeric($field) && is_string($fieldOptions)) {
			$field = $fieldOptions;
			$fieldOptions = [];
		}
		if (!empty($fieldOptions)) {
			$options = Hash::merge($fieldOptions, $options);
		}
		$this->CroogoForm->unlockField($field);
		echo $this->CroogoForm->input($field, $options);
	}

	echo $this->CroogoForm->submit(__d('croogo', 'Filter'));
	echo $this->CroogoForm->end();
?>
</div>
<?php endif; ?>
