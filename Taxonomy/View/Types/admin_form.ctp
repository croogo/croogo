<?php

$this->extend('/Common/admin_edit');

$this->Html->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Types'), array('plugin' => 'taxonomy', 'controller' => 'types', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->request->data['Type']['title'], '/' . $this->request->url);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

echo $this->Form->create('Type');

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Type'), '#type-main');
			echo $this->Croogo->adminTab(__d('croogo', 'Taxonomy'), '#type-taxonomy');
			echo $this->Croogo->adminTab(__d('croogo', 'Comments'), '#type-comments');
			echo $this->Croogo->adminTab(__d('croogo', 'Params'), '#type-params');
			echo $this->Croogo->adminTabs();
		?>
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
					'label' => __d('croogo', 'Title'),
				));
				echo $this->Form->input('alias', array(
					'label' => __d('croogo', 'Alias'),
				));
				echo $this->Form->input('description', array(
					'label' => __d('croogo', 'Description'),
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
						'0' => __d('croogo', 'Disabled'),
						'1' => __d('croogo', 'Read only'),
						'2' => __d('croogo', 'Read/Write'),
					),
					'default' => 2,
					'legend' => false,
					'label' => true,
					'class' => false,
				));
				echo $this->Form->input('comment_approve', array(
					'label' => 'Auto approve comments',
					'class' => false,
				));
				echo $this->Form->input('comment_spam_protection', array(
					'label' => __d('croogo', 'Spam protection (requires Akismet API key)'),
					'class' => false,
				));
				echo $this->Form->input('comment_captcha', array(
					'label' => __d('croogo', 'Use captcha? (requires Recaptcha API key)'),
					'class' => false,
				));
				echo $this->Html->link(__d('croogo', 'You can manage your API keys here.'), array(
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
					'label' => __d('croogo', 'Params'),
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
			$this->Form->input('format_show_author', array(
				'label' => __d('croogo', 'Show author\'s name'),
				'class' => false,
			)) .
			$this->Form->input('format_show_date', array(
				'label' => __d('croogo', 'Show date'),
				'class' => false,
			)) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
