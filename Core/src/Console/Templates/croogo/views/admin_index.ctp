<?php
$underscoredPluginName = $plugin ? Inflector::underscore($plugin) : 'default';
$header = <<<EOF
<?php
\$this->viewVars['title_for_layout'] = __d('$underscoredPluginName', '$pluralHumanName');
\$this->extend('/Common/$action');

\$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('$underscoredPluginName', '${pluralHumanName}'), array('action' => 'index'));

\$this->set('tableClass', 'table table-striped');


EOF;
echo $header;

foreach ($fields as $field):
	$columns[] = "\$this->Paginator->sort('{$field}')";
endforeach;
$columns[] = "array(__d('croogo', 'Actions') => array('class' => 'actions'))";

$columnList = implode(",\n\t\t", $columns);
$tableHeaders =<<<EOF
\$this->append('table-heading');
	\$tableHeaders = \$this->Html->tableHeaders(array(
		$columnList,
	));
	echo \$this->Html->tag('thead', \$tableHeaders);
\$this->end();


EOF;
echo $tableHeaders;

?>
<?php echo "\$this->append('table-body');\n"; ?>
<?php
	echo "\t\$rows = array();\n";
	echo "\tforeach (\${$pluralVar} as \${$singularVar}):\n";
	echo "\t\t\$row = array();\n";
		foreach ($fields as $field) {
			$isKey = false;
			if (!empty($associations['belongsTo'])) {
				foreach ($associations['belongsTo'] as $alias => $details) {
					if ($field === $details['foreignKey']) {
						$isKey = true;
						echo "\t\t\$row[] = \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array(\n\t\t\t'controller' => '{$details['controller']}',\n\t\t'action' => 'view',\n\t\t\t\${$singularVar}['{$alias}']['{$details['primaryKey']}'],\n\t));\n";
						break;
					}
				}
			}
			if ($isKey !== true) {
				switch ($schema[$field]['type']) {
					case 'datetime':
						echo "\t\t\$row[] = \$this->Time->format(\${$singularVar}['{$modelClass}']['{$field}'], '%Y-%m-%d %H:%M', __d('croogo', 'Invalid datetime'));\n";
						break;
					case 'boolean':
						echo "\t\t\$row[] = \$this->Html->status(\${$singularVar}['{$modelClass}']['{$field}']);\n";
						break;
					default:
						echo "\t\t\$row[] = h(\${$singularVar}['{$modelClass}']['{$field}']);\n";
				}
			}
		}

		echo "\t\t\$actions = array(\$this->Croogo->adminRowActions(\${$singularVar}['{$modelClass}']['{$primaryKey}']));\n";
		echo "\t\t\$actions[] = \$this->Croogo->adminRowAction('', array(\n\t\t\t'action' => 'view', \${$singularVar}['{$modelClass}']['{$primaryKey}']\n\t), array(\n\t\t\t'icon' => 'eye-open',\n\t\t));\n";
		echo "\t\t\$actions[] = \$this->Croogo->adminRowAction('', array(\n\t\t\t'action' => 'edit',\n\t\t\t\${$singularVar}['{$modelClass}']['{$primaryKey}'],\n\t\t), array(\n\t\t\t'icon' => 'pencil',\n\t\t));\n";
		echo "\t\t\$actions[] = \$this->Croogo->adminRowAction('', array(\n\t\t\t'action' => 'delete',\n\t\t\t\${$singularVar}['{$modelClass}']['{$primaryKey}'],\n\t\t), array(\n\t\t\t'icon' => 'trash',\n\t\t\t'escape' => true,\n\t\t),\n\t\t__d('croogo', 'Are you sure you want to delete # %s?', \${$singularVar}['{$modelClass}']['{$primaryKey}'])\n\t\t);\n";
		echo "\t\t\$row[] = \$this->Html->div('item-actions', implode(' ', \$actions));\n";
		echo "\t\t\$rows[] = \$this->Html->tableCells(\$row);\n";
	echo "\tendforeach;\n";
	echo "\techo \$this->Html->tag('tbody', implode('', \$rows));\n";
echo "\$this->end();\n";
