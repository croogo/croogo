<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Attachments'), array('plugin' => 'file_manager', 'controller' => 'attachments', 'action' => 'index'))
	->addCrumb(__('Upload'), $this->here)
;

$formUrl = array('controller' => 'attachments', 'action' => 'add');
if (isset($this->params['named']['editor'])) {
	$formUrl['editor'] = 1;
}
echo $this->Form->create('Node', array('url' => $formUrl, 'type' => 'file'));

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#attachment-upload" data-toggle="tab"><?php echo __('Upload'); ?></a></li>
		</ul>

		<div class="tab-content">

			<div id="attachment-upload" class="tab-pane">
			<?php
			echo $this->Form->input('Node.file', array('label' => __('Upload'), 'type' => 'file'));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
	<?php
		echo $this->Html->beginBox(__('Publishing')) .
			$this->Form->button(__('Upload'), array('button' => 'default')) .
			$this->Form->end() .
			$this->Html->link(__('Cancel'), array('action' => 'index'), array('button' => 'danger')) .
			$this->Html->endBox();
		echo $this->Croogo->adminBoxes();
	?>
	</div>

</div>
<?php echo $this->Form->end(); ?>