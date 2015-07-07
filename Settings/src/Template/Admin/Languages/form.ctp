<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Settings'), array('plugin' => 'settings', 'controller' => 'settings', 'action' => 'prefix', 'Site'))
	->addCrumb(__d('croogo', 'Language'), array('plugin' => 'settings', 'controller' => 'languages', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->request->data['Language']['title'], '/' . $this->request->url);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

echo $this->Form->create('Language');

?>
<div class="<?php echo $this->Theme->getCssClass('row'); ?>">
	<div class="<?php echo $this->Theme->getCssClass('columnLeft'); ?>">
		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Language'), '#language-main');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">
			<div id="language-main" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('title', array(
					'label' => __d('croogo', 'Title'),
				));
				echo $this->Form->input('native', array(
					'label' => __d('croogo', 'Native'),
				));
				echo $this->Form->input('alias', array(
					'label' => __d('croogo', 'Alias'),
				));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="<?php echo $this->Theme->getCssClass('columnRight'); ?>">
		<?php
			echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
				$this->Form->button(__d('croogo', 'Save'), array('button' => 'default')) .
				$this->Html->link(
					__d('croogo', 'Cancel'),
					array('action' => 'index'),
					array('class' => 'cancel', 'button' => 'danger')
				) .
				$this->Form->input('status', array(
					'label' => __d('croogo', 'Status'),
				)) .
				$this->Html->endBox();

			echo $this->Croogo->adminBoxes();
		?>
	</div>
</div>
<?php echo $this->Form->end(); ?>