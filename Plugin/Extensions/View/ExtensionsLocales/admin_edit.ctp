<?php
$this->extend('/Common/admin_edit');
$this->set('className', 'extensions-locales');
?>

<?php
$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Extensions'), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'))
	->addCrumb(__('Locales'), array('plugin' => 'extensions', 'controller' => 'extensions_themes', 'action' => 'index'))
	->addCrumb(__('Edit'));

echo $this->Form->create('Locale', array(
	'url' => array(
		'plugin' => 'extensions',
		'controller' => 'extensions_locales',
		'action' => 'edit',
		$locale
	),
));

?>
<div class="row-fluid">
	<div class="span8">
	<?php
		echo $this->Html->beginBox(__('Content')) .
			$this->Form->input('Locale.content', array(
				'value' => $content,
				'type' => 'textarea',
				'class' => 'content span10',
			)) .
			$this->Html->endBox();
	?>	
	</div>
	<div class="span4">
		<?php
			echo $this->Html->beginBox(__('Actions')) .
				$this->Form->button(__('Save'), array('button' => 'primary')) .
				$this->Html->link(__('Cancel'),
					array('action' => 'index'),
					array('button' => 'danger')
				) .
				$this->Html->endBox();
		?>
	</div>
</div>
