<?php

$header =<<<EOF
<?php
\$this->viewVars['title_for_layout'] = __('$pluralHumanName');
\$this->extend('/Common/admin_edit');

\$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('${pluralHumanName}'), array('action' => 'index'));

if (\$this->action == 'admin_edit') {
	\$this->Html->addCrumb(\$this->data['$modelClass']['$displayField'], \$this->here);
	\$this->viewVars['title_for_layout'] = '$pluralHumanName: ' . \$this->data['$modelClass']['$displayField'];
} else {
	\$this->Html->addCrumb(__('Add'), \$this->here);
}

echo \$this->Form->create('{$modelClass}');

?>\n
EOF;
echo $header;

$primaryTab = strtolower(Inflector::slug($singularHumanName, '-'));

?>
<div class="<?php echo $pluralVar; ?> row-fluid">
	<div class="span8">
		<ul class="nav nav-tabs">
			<li><a href="#<?php echo $primaryTab; ?>" data-toggle="tab"><?php echo "<?php echo __('$singularHumanName'); ?>"; ?></a></li>
			<?php echo "<?php echo \$this->Croogo->adminTabs(); ?>\n"; ?>
		</ul>

		<div class="tab-content">
			<div id='<?php echo $primaryTab; ?>' class="tab-pane">
<?php
				echo "\t\t\t<?php\n";
				echo "\t\t\t\techo \$this->Form->input('{$primaryKey}');\n";
				echo "\t\t\t\t\$this->Form->inputDefaults(array('label' => false, 'class' => 'span10'));\n";
				foreach ($fields as $field) {
					if ($field == $primaryKey) {
						continue;
					} elseif (!in_array($field, array('created', 'modified', 'updated'))) {
						$fieldLabel = Inflector::humanize($field);
						echo <<<EOF
				echo \$this->Form->input('{$field}', array(
					'placeholder' => '$fieldLabel',
				));\n
EOF;
					}
				}
				if (!empty($associations['hasAndBelongsToMany'])) {
					foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData) {
						echo "\t\t\t\techo \$this->Form->input('{$assocName}');\n";
					}
				}
				echo "\t\t\t\techo \$this->Croogo->adminTabs();\n";
				echo "\t\t\t?>\n";
?>
			</div>
		</div>

	</div>

	<div class="span4">
	<?php
		echo <<<EOF
<?php
		echo \$this->Html->beginBox(__('Publishing')) .
			\$this->Form->button(__('Apply'), array('name' => 'apply')) .
			\$this->Form->button(__('Save'), array('class' => 'btn btn-primary')) .
			\$this->Html->link(__('Cancel'), array('action' => 'index'), array('class' => 'btn btn-danger')) .
			\$this->Html->endBox();
		?>\n
EOF;
	?>
	</div>

</div>
<?php echo "<?php echo \$this->Form->end(); ?>\n"; ?>
