<?php

$header =<<<EOF
<?php
\$this->viewVars['title_for_layout'] = __d('croogo', '$pluralHumanName');
\$this->extend('/Common/$action');

\$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', '${pluralHumanName}'), array('action' => 'index'));

?>\n
EOF;
echo $header;

?>

<div class="<?php echo $pluralVar; ?> index">
	<table class="table table-striped">
	<tr>
	<?php foreach ($fields as $field): ?>
	<th><?php echo "<?php echo \$this->Paginator->sort('{$field}'); ?>"; ?></th>
	<?php endforeach; ?>
	<th class="actions"><?php echo "<?php echo __d('croogo', 'Actions'); ?>"; ?></th>
	</tr>
	<?php
	echo "<?php foreach (\${$pluralVar} as \${$singularVar}): ?>\n";
	echo "\t<tr>\n";
		foreach ($fields as $field) {
			$isKey = false;
			if (!empty($associations['belongsTo'])) {
				foreach ($associations['belongsTo'] as $alias => $details) {
					if ($field === $details['foreignKey']) {
						$isKey = true;
						echo "\t\t<td>\n\t\t\t<?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?>\n\t\t</td>\n";
						break;
					}
				}
			}
			if ($isKey !== true) {
				echo "\t\t<td><?php echo h(\${$singularVar}['{$modelClass}']['{$field}']); ?>&nbsp;</td>\n";
			}
		}

		echo "\t\t<td class=\"item-actions\">\n";
		echo "\t\t\t<?php echo \$this->Croogo->adminRowAction('', array('action' => 'view', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('icon' => 'eye-open')); ?>\n";
		echo "\t\t\t<?php echo \$this->Croogo->adminRowAction('', array('action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('icon' => 'pencil')); ?>\n";
		echo "\t\t\t<?php echo \$this->Croogo->adminRowAction('', array('action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('icon' => 'trash'), __d('croogo', 'Are you sure you want to delete # %s?', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?>\n";
		echo "\t\t</td>\n";
	echo "\t</tr>\n";

	echo "<?php endforeach; ?>\n";
	?>
	</table>
</div>
