<?php
$this->extend('/Common/admin_edit');
$this->set('className', 'extensions-locales');
?>

<?php
$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Extensions'), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'))
	->addCrumb(__('Locales'), array('plugin' => 'extensions', 'controller' => 'extensions_locales', 'action' => 'index'))
	->addCrumb($this->params['pass'][0], $this->here);

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

		<ul class="nav nav-tabs">
			<li><a href="#locale-content" data-toggle="tab"><?php echo __('Content'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">
			<div class="locale-content" class="tab-pane">
			<?php
				echo $this->Form->input('Locale.content', array(
					'label' => false,
					'placeholder' => __('Content'),
					'data-placement' => 'top',
					'value' => $content,
					'type' => 'textarea',
					'class' => 'span10',
				));
			?>
			</div>
			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
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
