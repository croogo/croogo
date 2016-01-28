<?php

use Croogo\Core\Status;

$this->extend('Croogo/Core./Common/admin_edit');
//$this->Html->script(array('Croogo/Nodes.admin'), false);

$this->CroogoHtml
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), array('action' => 'index'));

if ($this->request->params['action'] == 'add') {
	$this->assign('title', __d('croogo', 'Create content: %s', $type->title));

	$formUrl = array('action' => 'add', $typeAlias);
	$this->CroogoHtml
		->addCrumb(__d('croogo', 'Create'), array('action' => 'create'))
		->addCrumb($type->title, '/' . $this->request->url);
}

if ($this->request->params['action'] == 'edit') {
	$formUrl = array('action' => 'edit');
	$this->CroogoHtml->addCrumb($node->title, '/' . $this->request->url);
}

$lookupUrl = $this->Url->build([
	'plugin' => 'Croogo/Users',
	'controller' => 'Users',
	'action' => 'lookup',
	'_ext' => 'json'
]);

$parentTitle = isset($parentTitle) ? $parentTitle : null;
$apiUrl = $this->Url->build([
	'action' => 'lookup',
	'_ext' => 'json',
	'?' => [
		'type' => $type->alias,
	]
]);

echo $this->CroogoForm->create($node, array(
	'url' => $formUrl,
	'class' => 'protected-form',
));

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', $type->title), '#node-main');
			echo $this->Croogo->adminTab(__d('croogo', 'Access'), '#node-access');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">

			<div id="node-main" class="tab-pane">
			<?php

				echo $this->CroogoForm->autocomplete('parent_id', array(
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
				echo $this->CroogoForm->input('id');
				$this->CroogoForm->templates(array(
					'class' => 'span10',
				));
				echo $this->CroogoForm->input('title', array(
					'label' => __d('croogo', 'Title'),
				));
				echo $this->CroogoForm->input('slug', array(
					'class' => 'span10 slug',
					'label' => __d('croogo', 'Slug'),
				));
				echo $this->CroogoForm->input('excerpt', array(
					'label' => __d('croogo', 'Excerpt'),
				));
				echo $this->CroogoForm->input('body', array(
					'label' => __d('croogo', 'Body'),
					'id' => 'NodeBody'
				));
			?>
			</div>

			<div id="node-access" class="tab-pane">
			<?php
				echo $this->CroogoForm->input('Role.Role', array('class' => false, 'multiple' => true));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>

	</div>
	<div class="span4">
	<?php
		$username = isset($node->user->username) ?
			$node->user->username :
			$this->request->session()->read('Auth.User.username');
		echo $this->CroogoHtml->beginBox(__d('croogo', 'Publishing')) .
			$this->CroogoForm->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
			$this->CroogoForm->button(__d('croogo', 'Save'), array('button' => 'success')) .
			$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('class' => 'cancel btn btn-danger')) .
			$this->CroogoForm->input('status', array(
				'legend' => false,
				'label' => false,
				'type' => 'radio',
				'class' => false,
				'default' => Status::UNPUBLISHED,
				'options' => $this->Croogo->statuses(),
			)) .
			$this->CroogoForm->input('promote', array(
				'label' => __d('croogo', 'Promoted to front page'),
				'class' => false,
			)) .
			$this->CroogoForm->autocomplete('user_id', array(
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

			$this->CroogoForm->input('created', array(
				'type' => 'text',
				'class' => 'span10 input-datetime',
			)) .

			$this->Html->div('input-daterange',
				$this->CroogoForm->input('publish_start', array(
					'label' => __d('croogo', 'Publish Start'),
					'type' => 'text',
				)) .
				$this->CroogoForm->input('publish_end', array(
					'label' => __d('croogo', 'Publish End'),
					'type' => 'text',
				))
			);

		echo $this->CroogoHtml->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->CroogoForm->end(); ?>
