<?php
$underscoredPluginName = Inflector::underscore($plugin);
$header = <<<EOF
<?php
\$this->viewVars['title_for_layout'] = __d('$underscoredPluginName', '$pluralHumanName');
\$this->extend('/Common/admin_edit');

\$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('$underscoredPluginName', '${pluralHumanName}'), array('action' => 'index'));

if (\$this->action == 'admin_edit') {
	\$this->Html->addCrumb(\$this->request->data['$modelClass']['$displayField'], '/' . \$this->request->url);
	\$this->viewVars['title_for_layout'] = '$pluralHumanName: ' . \$this->request->data['$modelClass']['$displayField'];
} else {
	\$this->Html->addCrumb(__d('croogo', 'Add'), '/' . \$this->request->url);
}

\$this->append('form-start', \$this->Form->create('{$modelClass}'));


EOF;
echo $header;

$primaryTab = strtolower(Inflector::slug($singularHumanName, '-'));

echo "\$this->append('tab-heading');\n";
	echo "\techo \$this->Croogo->adminTab(__d('$underscoredPluginName', '$singularHumanName'), '#$primaryTab');\n";
	echo "\techo \$this->Croogo->adminTabs();\n";
echo "\$this->end();\n\n";

echo "\$this->append('tab-content');\n";
	echo "\techo \$this->Form->input('{$primaryKey}');\n";
	foreach ($fields as $field):
		if ($field == $primaryKey):
			continue;
		elseif (!in_array($field, array('created', 'modified', 'updated'))):
			$fieldLabel = Inflector::humanize($field);
			echo <<<EOF
	echo \$this->Form->input('{$field}', array(
		'label' => '$fieldLabel',
	));\n
EOF;
		endif;
	endforeach;

	if (!empty($associations['hasAndBelongsToMany'])):
		foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData):
			echo "\ttecho \$this->Form->input('{$assocName}');\n";
		endforeach;
	endif;

	echo "\techo \$this->Croogo->adminTabs();\n";
echo "\$this->end();\n\n";

echo <<<EOF
\$this->append('panels');
	echo \$this->Html->beginBox(__d('croogo', 'Publishing')) .
		\$this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
		\$this->Form->button(__d('croogo', 'Save'), array('button' => 'primary')) .
		\$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('button' => 'danger'));
	echo \$this->Html->endBox();

	echo \$this->Croogo->adminBoxes();
\$this->end();


EOF;

echo "\$this->append('form-end', \$this->Form->end());\n";
