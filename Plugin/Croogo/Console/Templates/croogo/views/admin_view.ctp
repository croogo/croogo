<?php

$header =<<<EOF
<?php
\$this->viewVars['title_for_layout'] = sprintf('%s: %s', __d('croogo', '$pluralHumanName'), h(\${$singularVar}['$modelClass']['$displayField']));

\$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', '${pluralHumanName}'), array('action' => 'index'));
	
?>\n
EOF;

echo $header;
?>
<h2 class="hidden-desktop"><?php echo "<?php echo __d('croogo', '{$singularHumanName}'); ?>"; ?></h2>

<div class="row-fluid">
	<div class="span12 actions">
		<ul class="nav-buttons">
<?php
	echo "\t\t<li><?php echo \$this->Html->link(__d('croogo', 'Edit " . $singularHumanName ."'), array('action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('button' => 'default')); ?> </li>\n";
	echo "\t\t<li><?php echo \$this->Form->postLink(__d('croogo', 'Delete " . $singularHumanName . "'), array('action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('button' => 'default'), __d('croogo', 'Are you sure you want to delete # %s?', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?> </li>\n";
	echo "\t\t<li><?php echo \$this->Html->link(__d('croogo', 'List " . $pluralHumanName . "'), array('action' => 'index'), array('button' => 'default')); ?> </li>\n";
	echo "\t\t<li><?php echo \$this->Html->link(__d('croogo', 'New " . $singularHumanName . "'), array('action' => 'add'), array('button' => 'default')); ?> </li>\n";

	$done = array();
	foreach ($associations as $type => $data) {
		foreach ($data as $alias => $details) {
			if ($details['controller'] != $this->name && !in_array($details['controller'], $done)) {
				echo "\t\t<li><?php echo \$this->Html->link(__d('croogo', 'List " . Inflector::humanize($details['controller']) . "'), array('controller' => '{$details['controller']}', 'action' => 'index')); ?> </li>\n";
				echo "\t\t<li><?php echo \$this->Html->link(__d('croogo', 'New " .  Inflector::humanize(Inflector::underscore($alias)) . "'), array('controller' => '{$details['controller']}', 'action' => 'add')); ?> </li>\n";
				$done[] = $details['controller'];
			}
		}
	}
?>
		</ul>
	</div>
</div>

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
		echo "\t\t<dd>\n\t\t\t<?php echo h(\${$singularVar}['{$modelClass}']['{$field}']); ?>\n\t\t\t&nbsp;\n\t\t</dd>\n";
	}
}
?>
	</dl>
</div>
