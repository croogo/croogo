<?php

$this->extend('/Common/admin_edit');

$this->Html->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__('Types'), array('plugin' => 'taxonomy', 'controller' => 'types', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->request->data['Type']['title'], $this->here);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__('Add'), $this->here);
}

echo $this->Form->create('Type');

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#type-main" data-toggle="tab"><?php echo __('Type'); ?></a></li>
			<li><a href="#type-taxonomy" data-toggle="tab"><?php echo __('Taxonomy'); ?></a></li>
			<li><a href="#type-comments" data-toggle="tab"><?php echo __('Comments'); ?></a></li>
			<li><a href="#type-params" data-toggle="tab"><?php echo __('Params'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">
			<div id="type-main" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				$this->Form->inputDefaults(array(
					'label' => false,
					'class' => 'span10',
				));
				echo $this->Form->input('title', array(
					'placeholder' => __('Title'),
				));
				echo $this->Form->input('alias', array(
					'placeholder' => __('Alias'),
				));
				echo $this->Form->input('description', array(
					'placeholder' => __('Description'),
				));
			?>
			</div>

			<div id="type-taxonomy" class="tab-pane">
			<?php
				echo $this->Form->input('Vocabulary.Vocabulary', array(
					'class' => false,
				));
			?>
			</div>

			<div id="type-comments" class="tab-pane">
			<?php
				echo $this->Form->input('comment_status', array(
					'type' => 'radio',
					'options' => array(
						'0' => __('Disabled'),
						'1' => __('Read only'),
						'2' => __('Read/Write'),
					),
					'value' => 2,
					'legend' => false,
					'label' => true,
					'class' => false,
				));
				echo $this->Form->input('comment_approve', array(
					'label' => 'Auto approve comments',
					'class' => false,
				));
				echo $this->Form->input('comment_spam_protection', array(
					'label' => __('Spam protection (requires Akismet API key)'),
					'class' => false,
				));
				echo $this->Form->input('comment_captcha', array(
					'label' => __('Use captcha? (requires Recaptcha API key)'),
					'class' => false,
				));
				echo $this->Html->link(__('You can manage your API keys here.'), array(
					'plugin' => 'settings',
					'controller' => 'settings',
					'action' => 'prefix',
					'Service'
				));
			?>
			</div>

			<div id="type-params" class="tab-pane">
			<?php
				echo $this->Form->input('Type.params', array(
					'placeholder' => __('Params'),
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
			$this->Form->input('format_show_author', array(
				'label' => __('Show author\'s name'),
				'class' => false,
			)) .
			$this->Form->input('format_show_date', array(
				'label' => __('Show date'),
				'class' => false,
			)) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
