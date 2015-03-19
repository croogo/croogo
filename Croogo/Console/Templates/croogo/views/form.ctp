<?php
$i18nDomain = $plugin ? Inflector::underscore($plugin) : 'default';
$header = <<<EOF
<?php
\$this->viewVars['title_for_layout'] = __d('$i18nDomain', '$pluralHumanName');
\$this->extend('/Common/admin_edit');

\$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('$i18nDomain', '${pluralHumanName}'), array('action' => 'index'));

if (\$this->action == 'admin_edit') {
	\$this->Html->addCrumb(\$this->request->data['$modelClass']['$displayField'], '/' . \$this->request->url);
	\$this->viewVars['title_for_layout'] = __d('$i18nDomain', '$pluralHumanName') . ': ' . \$this->request->data['$modelClass']['$displayField'];
} else {
	\$this->Html->addCrumb(__d('croogo', 'Add'), '/' . \$this->request->url);
}

\$this->append('form-start', \$this->Form->create('{$modelClass}'));


EOF;
echo $header;

$primaryTab = strtolower(Inflector::slug($singularHumanName, '-'));

echo "\$this->append('tab-heading');\n";
	echo "\techo \$this->Croogo->adminTab(__d('$i18nDomain', '$singularHumanName'), '#$primaryTab');\n";
	echo "\techo \$this->Croogo->adminTabs();\n";
echo "\$this->end();\n\n";

echo "\$this->append('tab-content');\n\n";

	echo "\techo \$this->Html->tabStart('{$primaryTab}');\n\n";

	echo "\t\techo \$this->Form->input('{$primaryKey}');\n\n";

	foreach ($fields as $field):
		if ($field == $primaryKey):
			continue;
		elseif (!in_array($field, array('created', 'modified', 'updated', 'created_by', 'updated_by'))):
			$fieldLabel = strrpos($field, '_id', -3) ? substr($field, 0, -3) : $field;
			$fieldLabel = Inflector::humanize($fieldLabel);
			echo <<<EOF
		echo \$this->Form->input('{$field}', array(
			'label' =>  __d('$i18nDomain', '$fieldLabel'),
		));\n
EOF;
		endif;
	endforeach;

	if (!empty($associations['hasAndBelongsToMany'])):
		foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData):
			echo "\t\techo \$this->Form->input('{$assocName}');\n";
		endforeach;
	endif;

	echo "\n";
	echo "\techo \$this->Html->tabEnd();\n\n";

	echo "\techo \$this->Croogo->adminTabs();\n\n";

echo "\$this->end();\n\n";

echo <<<EOF
\$this->append('panels');
	echo \$this->Html->beginBox(__d('croogo', 'Publishing')) .
		\$this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
		\$this->Form->button(__d('croogo', 'Save'), array('button' => 'primary')) .
		\$this->Form->button(__d('croogo', 'Save & New'), array('button' => 'success', 'name' => 'save_and_new')) .
		\$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('button' => 'danger'));
	echo \$this->Html->endBox();

	echo \$this->Croogo->adminBoxes();
\$this->end();


EOF;

echo "\$this->append('form-end', \$this->Form->end());\n";
