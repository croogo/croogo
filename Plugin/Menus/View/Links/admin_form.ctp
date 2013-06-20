<?php

$this->extend('/Common/admin_edit');
$this->Html->script(array('Menus.links'), false);

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Menus'), array('plugin' => 'menus', 'controller' => 'menus', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_add') {
	$this->Html
		->addCrumb($menus[$menuId], array(
			'plugin' => 'menus', 'controller' => 'links', 'action' => 'index',
			'?' => array('menu_id' => $menuId))
		)
		->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
	$formUrl = array(
		'controller' => 'links', 'action' => 'add', 'menu' => $menuId
	);
}

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html
		->addCrumb($this->data['Menu']['title'], array(
			'plugin' => 'menus', 'controller' => 'links', 'action' => 'index',
			'?' => array('menu_id' => $this->data['Menu']['id'])))
		->addCrumb($this->request->data['Link']['title'], '/' . $this->request->url);
	$formUrl = array(
		'controller' => 'links', 'action' => 'edit', 'menu' => $menuId
	);
}

echo $this->Form->create('Link', array('url' => $formUrl));

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Link'), '#link-basic');
			echo $this->Croogo->adminTab(__d('croogo', 'Access'), '#link-access');
			echo $this->Croogo->adminTab(__d('croogo', 'Misc.'), '#link-misc');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">
			<div id="link-basic" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('menu_id', array(
					'selected' => $menuId,
				));
				echo $this->Form->input('parent_id', array(
					'title' => __d('croogo', 'Parent'),
					'options' => $parentLinks,
					'empty' => true,
				));
				$this->Form->inputDefaults(array(
					'class' => 'span10',
				));
				echo $this->Form->input('title', array(
					'label' => __d('croogo', 'Title'),
				));
				echo $this->Form->input('link', array(
					'label' => __d('croogo', 'Link'),
				));
				echo $this->Html->link(__d('croogo', 'Link to a Node'), Router::url(array(
					'plugin' => 'nodes',
					'controller' => 'nodes',
					'action' => 'index',
					'?' => array(
						'chooser' => 1,
						'KeepThis' => true,
						'TB_iframe' => true,
						'height' => 400,
						'width' => 600,
					)), true),
					array(
						'class' => 'link chooser',
					)
				);
			?>
			</div>

			<div id="link-access" class="tab-pane">
			<?php
				echo $this->Form->input('Role.Role', array(
					'class' => false,
				));
			?>
			</div>

			<div id="link-misc" class="tab-pane">
			<?php
				echo $this->Form->input('class', array(
					'label' => __d('croogo', 'Class'),
				));
				echo $this->Form->input('description', array(
					'label' => __d('croogo', 'Description'),
				));
				echo $this->Form->input('rel', array(
					'label' => __d('croogo', 'Rel'),
				));
				echo $this->Form->input('target', array(
					'label' => __d('croogo', 'Target'),
				));
				echo $this->Form->input('params', array(
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
			$this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply', 'button' => 'default')) .
			$this->Form->button(__d('croogo', 'Save'), array('button' => 'default')) .
			$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('button' => 'danger')) .
			$this->Form->input('status', array(
				'label' => __d('croogo', 'Status'),
				'class' => false,
			)) .
			$this->Html->endBox();
		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
<?php
$script = <<<EOF
$('.link.chooser').itemChooser({
	fields: [{ type: "Node", target: "#LinkLink", attr: "rel" }]
});
EOF;
$this->Js->buffer($script);
