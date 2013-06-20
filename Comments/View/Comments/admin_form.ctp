<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Comments'), array('plugin' => 'comments', 'controller' => 'comments', 'action' => 'index'))
	->addCrumb($this->request->data['Comment']['id'], '/' . $this->request->url);

echo $this->Form->create('Comment');

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Comment'), '#comment-main');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">

			<div id="comment-main" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				$this->Form->inputDefaults(array(
					'class' => 'span10',
				));
				echo $this->Form->input('title', array(
					'label' => __d('croogo', 'Title'),
				));
				echo $this->Form->input('body', array(
					'label' => __d('croogo', 'Body'),
				));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
		<?php
			echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
				$this->Form->button(__d('croogo', 'Save'), array('button' => 'default')) .
				$this->Html->link(
					__d('croogo', 'Cancel'),
					array('action' => 'index'),
					array('button' => 'danger')
				) .
				$this->Form->input('status', array(
					'label' => __d('croogo', 'Published'),
					'class' => false,
				)) .
				$this->Html->endBox();

			echo $this->Html->beginBox(__d('croogo', 'Contact')) .
				$this->Form->input('name', array('label' => __d('croogo', 'Name'), 'class' => 'span12')) .
				$this->Form->input('email', array('label' => __d('croogo', 'Email'), 'class' => 'span12')) .
				$this->Form->input('website', array('label' => __d('croogo', 'Website'), 'class' => 'span12')) .
				$this->Form->input('ip', array('disabled' => 'disabled', 'label' => __d('croogo', 'Ip'))) .
				$this->Html->endBox();

			echo $this->Croogo->adminBoxes();
		?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
