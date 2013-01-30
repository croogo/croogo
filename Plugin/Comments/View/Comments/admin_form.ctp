<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__('Comments'), array('plugin' => 'comments', 'controller' => 'comments', 'action' => 'index'))
	->addCrumb($this->request->data['Comment']['id'], $this->here);

echo $this->Form->create('Comment');

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#comment-main" data-toggle="tab"><?php echo __('Comment'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">

			<div id="comment-main" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				$this->Form->inputDefaults(array(
					'class' => 'span10',
				));
				echo $this->Form->input('title', array(
					'placeholder' => __('Title'),
				));
				echo $this->Form->input('body', array(
					'placeholder' => __('Body'),
				));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
		<?php
			echo $this->Html->beginBox(__('Publishing')) .
				$this->Form->button(__('Save'), array('button' => 'default')) .
				$this->Html->link(
					__('Cancel'),
					array('action' => 'index'),
					array('button' => 'danger')
				) .
				$this->Form->input('status', array(
					'placeholder' => __('Published'),
					'class' => false,
				)) .
				$this->Html->endBox();

			echo $this->Html->beginBox(__('Contact')) .
				$this->Form->input('name', array('label' => __('Name'), 'class' => 'span12')) .
				$this->Form->input('email', array('label' => __('Email'), 'class' => 'span12')) .
				$this->Form->input('website', array('label' => __('Website'), 'class' => 'span12')) .
				$this->Form->input('ip', array('disabled' => 'disabled', 'label' => __('Ip'))) .
				$this->Html->endBox();

			echo $this->Croogo->adminBoxes();
		?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
