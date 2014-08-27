<?php

$this->extend('/Common/admin_edit');
$this->Croogo->adminScript('Nodes.admin');

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

echo $this->Form->create('Node', array(
	'url' => $formUrl,
	'class' => 'protected-form',
));
$inputDefaults = $this->Form->inputDefaults();
$inputClass = isset($inputDefaults['class']) ? $inputDefaults['class'] : null;

?>
<div class="<?php echo $this->Layout->cssClass('row'); ?>">
	<div class="<?php echo $this->Layout->cssClass('columnLeft'); ?>">

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
				));
				echo $this->Form->input('id');
				echo $this->Form->input('title', array(
					'label' => __d('croogo', 'Title'),
				));
				echo $this->Form->input('slug', array(
					'class' => trim($inputClass . ' slug'),
					'label' => __d('croogo', 'Slug'),
				));
				echo $this->Form->input('excerpt', array(
					'label' => __d('croogo', 'Excerpt'),
				));
				echo $this->Form->input('body', array(
					'label' => __d('croogo', 'Body'),
					'id' => ($type['Type']['format_use_wysiwyg'] ? 'NodeBody' : 'NodeBodyNoWysiwyg'),
				));
			?>
			</div>

			<div id="node-access" class="tab-pane">
			<?php
				echo $this->Form->input('Role.Role', array(
					'label' => __d('croogo', 'Role')
				));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>

	</div>
	<div class="<?php echo $this->Layout->cssClass('columnRight'); ?>">
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
				'class' => trim($inputClass . ' input-datetime'),
				'label' => __d('croogo', 'Created'),
			)) .

			$this->Html->div('input-daterange',
				$this->Form->input('publish_start', array(
					'label' => __d('croogo', 'Publish Start'),
					'type' => 'text',
				)) .
				$this->Form->input('publish_end', array(
					'label' => __d('croogo', 'Publish End'),
					'type' => 'text',
				))
			);

		echo $this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
