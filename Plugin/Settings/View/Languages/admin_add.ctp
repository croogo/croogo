<?php
$this->extend('Croogo./Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Settings'), array('plugin' => 'settings', 'controller' => 'settings', 'action' => 'prefix', 'Site'))
	->addCrumb(__('Language'), array('plugin' => 'settings', 'controller' => 'languages', 'action' => 'index'))
	->addCrumb(__('Add'));

echo $this->Form->create('Language');

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#language-main" data-toggle="tab"><?php echo __('Language'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">
			<div id="language-main" class="tab-pane">
			<?php
				$this->Form->inputDefaults(array(
					'class' => 'span10',
				));
				echo $this->Form->input('title');
				echo $this->Form->input('native');
				echo $this->Form->input('alias');
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>
	<div class="span4">
		<?php
			echo $this->Html->beginBox(__('Publishing')) .
				$this->Form->button(__('Save'), array('button' => 'default')).
				$this->Html->link(__('Cancel'),
					array('action' => 'index'),
					array('class' => 'cancel', 'button' => 'danger')
				) .
				$this->Form->input('status', array(
					'class' => false,
				)) .
				$this->Html->endBox();
			echo $this->Croogo->adminBoxes();
		?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
