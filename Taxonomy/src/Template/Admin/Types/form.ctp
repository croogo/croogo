<?php

$this->extend('Croogo/Croogo./Common/admin_edit');

$this->CroogoHtml->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Types'), array('plugin' => 'Croogo/Taxonomy', 'controller' => 'Types', 'action' => 'index'));

if ($this->request->params['action'] == 'edit') {
	$this->CroogoHtml->addCrumb($this->request->data['Type']['title'], '/' . $this->request->url);
}

if ($this->request->params['action'] == 'add') {
	$this->CroogoHtml->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

echo $this->CroogoForm->create($type);

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
				echo $this->CroogoForm->input('id');
				$this->CroogoForm->templates(array(
					'class' => 'span10',
				));
				echo $this->CroogoForm->input('title', array(
					'label' => __d('croogo', 'Title'),
				));
				echo $this->CroogoForm->input('alias', array(
					'label' => __d('croogo', 'Alias'),
				));
				echo $this->CroogoForm->input('description', array(
					'label' => __d('croogo', 'Description'),
				));
			?>
			</div>

			<div id="type-taxonomy" class="tab-pane">
			<?php
				echo $this->CroogoForm->input('Vocabulary.Vocabulary', array(
					'class' => false,
				));
			?>
			</div>

			<div id="type-comments" class="tab-pane">
			<?php
				echo $this->CroogoForm->input('comment_status', array(
					'type' => 'radio',
					'options' => array(
						'0' => __d('croogo', 'Disabled'),
						'1' => __d('croogo', 'Read only'),
						'2' => __d('croogo', 'Read/Write'),
					),
					'default' => 2,
					'legend' => false,
					'label' => __d('croogo', 'Comment status'),
					'class' => false,
				));
				echo $this->CroogoForm->input('comment_approve', array(
					'label' => 'Auto approve comments',
					'class' => false,
				));
				echo $this->CroogoForm->input('comment_spam_protection', array(
					'label' => __d('croogo', 'Spam protection (requires Akismet API key)'),
					'class' => false,
				));
				echo $this->CroogoForm->input('comment_captcha', array(
					'label' => __d('croogo', 'Use captcha? (requires Recaptcha API key)'),
					'class' => false,
				));
				echo $this->CroogoHtml->link(__d('croogo', 'You can manage your API keys here.'), array(
					'plugin' => 'Croogo/Settings',
					'controller' => 'Settings',
					'action' => 'prefix',
					'Service'
				));
			?>
			</div>

			<div id="type-params" class="tab-pane">
			<?php
				echo $this->CroogoForm->input('Type.params', array(
					'label' => __d('croogo', 'Params'),
				));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
	<?php
		echo $this->CroogoHtml->beginBox(__d('croogo', 'Publishing')) .
			$this->CroogoForm->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
			$this->CroogoForm->button(__d('croogo', 'Save'), array('button' => 'success')) .
			$this->CroogoHtml->link(
				__d('croogo', 'Cancel'),
				array('action' => 'index'),
				array('button' => 'danger')
			) .
			$this->CroogoForm->input('format_show_author', array(
				'label' => __d('croogo', 'Show author\'s name'),
				'class' => false,
			)) .
			$this->CroogoForm->input('format_show_date', array(
				'label' => __d('croogo', 'Show date'),
				'class' => false,
			)) .
			$this->CroogoHtml->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->CroogoForm->end(); ?>
