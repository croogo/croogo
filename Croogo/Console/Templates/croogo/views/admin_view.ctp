<?php

$header = <<<EOF
<?php

\$this->extend('/Common/admin_view');
\$this->viewVars['title_for_layout'] = sprintf('%s: %s', __d('croogo', '$pluralHumanName'), h(\${$singularVar}['$modelClass']['$displayField']));

\$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', '${pluralHumanName}'), array('action' => 'index'));

if (isset(\${$singularVar}['$modelClass']['$displayField'])):
	\$this->Html->addCrumb(\${$singularVar}['$modelClass']['$displayField'], '/' . \$this->request->url);
endif;


EOF;

echo $header;

echo "\$this->set('title', __d('croogo', '{$singularHumanName}'));\n\n";

echo "\$this->append('actions');\n";
	echo "\techo \$this->Croogo->adminAction(__d('croogo', 'Edit " . $singularHumanName . "'), array(\n\t\t'action' => 'edit',\n\t\t\${$singularVar}['{$modelClass}']['{$primaryKey}'],\n\t), array(\n\t\t'button' => 'default',\n\t));\n";
	echo "\techo \$this->Croogo->adminAction(__d('croogo', 'Delete " . $singularHumanName . "'), array(\n\t\t'action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}'],\n\t), array(\n\t\t'method' => 'post',\n\t\t'button' => 'danger',\n\t\t'escapeTitle' => true,\n\t\t'escape' => false,\n\t),\n\t__d('croogo', 'Are you sure you want to delete # %s?', \${$singularVar}['{$modelClass}']['{$primaryKey}'])\n\t);\n";
	echo "\techo \$this->Croogo->adminAction(__d('croogo', 'List " . $pluralHumanName . "'), array('action' => 'index'));\n";
	echo "\techo \$this->Croogo->adminAction(__d('croogo', 'New " . $singularHumanName . "'), array('action' => 'add'), array('button' => 'success'));\n";

$done = array();
$excludeAssociations = array(
	'TrackableCreator',
	'TrackableUpdater',
);

foreach ($associations as $type => $data):
	foreach ($data as $alias => $details):
		if (in_array($alias, $excludeAssociations)):
			continue;
		endif;
		if ($details['controller'] != $this->name && !in_array($details['controller'], $done)):
			echo "\techo \$this->Croogo->adminAction(__d('croogo', 'List " . Inflector::humanize($details['controller']) . "'), array('controller' => '{$details['controller']}', 'action' => 'index'));\n";
			echo "\techo \$this->Croogo->adminAction(__d('croogo', 'New " . Inflector::humanize(Inflector::underscore($alias)) . "'), array('controller' => '{$details['controller']}', 'action' => 'add'));\n";
			$done[] = $details['controller'];
		endif;
	endforeach;
endforeach;

echo "\$this->end();\n\n";

echo "\$this->append('main');\n?>\n";

?>
<div class="<?php echo $pluralVar; ?> view">
	<dl class="inline">
<?php
foreach ($fields as $field) {
	$isKey = false;
	if (!empty($associations['belongsTo'])) {
		foreach ($associations['belongsTo'] as $alias => $details) {
			if ($field === $details['foreignKey']) {
				$isKey = true;
				echo "\t\t<dt><?php echo __d('croogo', '" . Inflector::humanize(Inflector::underscore($alias)) . "'); ?></dt>\n";
				echo "\t\t<dd>\n\t\t\t<?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?>\n\t\t\t&nbsp;\n\t\t</dd>\n";
				break;
			}
		}
	}
	if ($isKey !== true) {
		echo "\t\t<dt><?php echo __d('croogo', '" . Inflector::humanize($field) . "'); ?></dt>\n";
		switch ($schema[$field]['type']) {
			case 'datetime':
				echo "\t\t<dd>\n\t\t\t<?php echo \$this->Time->format(\${$singularVar}['{$modelClass}']['{$field}'], '%Y-%m-%d %H:%M:%S', __d('croogo', 'Invalid datetime')); ?>\n\t\t\t&nbsp;\n\t\t</dd>\n";
				break;
			case 'boolean':
				echo "\t\t<dd>\n\t\t\t<?php echo \$this->Html->status(\${$singularVar}['{$modelClass}']['{$field}']); ?>\n\t\t\t&nbsp;\n\t\t</dd>\n";
				break;
			default:
				echo "\t\t<dd>\n\t\t\t<?php echo h(\${$singularVar}['{$modelClass}']['{$field}']); ?>\n\t\t\t&nbsp;\n\t\t</dd>\n";
		}
	}
}
?>
	</dl>
</div>
<?php

echo "<?php \$this->end(); ?>";
