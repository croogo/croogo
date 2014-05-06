<?php

$this->extend('/Common/admin_edit');
$this->Html->script(array('Nodes.admin'), false);

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), array('controller' => 'nodes', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_add') {
	$formUrl = array('action' => 'add', $typeAlias);
	$this->Html
		->addCrumb(__d('croogo', 'Create'), array('controller' => 'nodes', 'action' => 'create'))
		->addCrumb($type['Type']['title'], '/' . $this->request->url);
}

if ($this->request->params['action'] == 'admin_edit') {
	$formUrl = array('action' => 'edit');
	$this->Html->addCrumb($this->request->data['Node']['title'], '/' . $this->request->url);
}

$lookupUrl = $this->Html->apiUrl(array(
	'plugin' => 'users',
	'controller' => 'users',
	'action' => 'lookup',
));

$parentTitle = isset($parentTitle) ? $parentTitle : null;
$apiUrl = $this->Form->apiUrl(array(
	'controller' => 'nodes',
	'action' => 'lookup',
	'?' => array(
		'type' => $type['Type']['alias'],
	),
));

echo $this->Form->create('Node', array('url' => $formUrl));

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', $type['Type']['title']), '#node-main');
			echo $this->Croogo->adminTab(__d('croogo', 'Access'), '#node-access');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">

			<div id="node-main" class="tab-pane">
			<?php

				echo $this->Form->autocomplete('parent_id', array(
					'label' => __d('croogo', 'Parent'),
					'type' => 'text',
					'autocomplete' => array(
						'default' => $parentTitle,
						'data-displayField' => 'title',
						'data-primaryKey' => 'id',
						'data-queryField' => 'title',
						'data-relatedElement' => '#NodeParentId',
						'data-url' => $apiUrl,
					),
					'class' => 'span10',
				));
				echo $this->Form->input('id');
				$this->Form->inputDefaults(array(
					'class' => 'span10',
				));
				echo $this->Form->input('title', array(
					'label' => __d('croogo', 'Title'),
				));
				echo $this->Form->input('slug', array(
					'class' => 'span10 slug',
					'label' => __d('croogo', 'Slug'),
				));
				echo $this->Form->input('excerpt', array(
					'label' => __d('croogo', 'Excerpt'),
				));
				echo $this->Form->input('body', array(
					'label' => __d('croogo', 'Body'),
				));
			?>
			</div>

			<div id="node-access" class="tab-pane">
			<?php
				echo $this->Form->input('Role.Role', array('class' => false));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>

	</div>
	<div class="span4">
	<?php
		$username = isset($this->data['User']['username']) ?
			$this->data['User']['username'] :
			$this->Session->read('Auth.User.username');
		echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
			$this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
			$this->Form->button(__d('croogo', 'Save'), array('button' => 'success')) .
			$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('class' => 'cancel btn btn-danger')) .
			$this->Form->input('status', array(
				'legend' => false,
				'type' => 'radio',
				'class' => false,
				'default' => CroogoStatus::UNPUBLISHED,
				'options' => $this->Croogo->statuses(),
			)) .
			$this->Form->input('promote', array(
				'label' => __d('croogo', 'Promoted to front page'),
				'class' => false,
			)) .
			$this->Form->autocomplete('user_id', array(
				'type' => 'text',
				'label' => __d('croogo', 'Publish as '),
				'class' => 'span10',
				'autocomplete' => array(
					'default' => $username,
					'data-displayField' => 'username',
					'data-primaryKey' => 'id',
					'data-queryField' => 'name',
					'data-relatedElement' => '#NodeUserId',
					'data-url' => $lookupUrl,
				),
			)) .

			$this->Form->input('created', array(
				'type' => 'text',
				'class' => 'span10 input-datetime',
			));

		echo $this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>