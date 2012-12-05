<?php

$this->extend('/Common/admin_edit');
$this->Html->script(array('Menus.links'), false);

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Menus'), array('plugin' => 'menus', 'controller' => 'menus', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_add') {
	$this->Html
		->addCrumb($menus[$menuId], array(
			'plugin' => 'menus', 'controller' => 'links', 'action' => 'index',
			'?' => array('menu_id' => $menuId))
		)
		->addCrumb(__('Add'), $this->here);
	$formUrl = array(
		'controller' => 'links', 'action' => 'add', 'menu' => $menuId
	);
}

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html
		->addCrumb($this->data['Menu']['title'], array(
			'plugin' => 'menus', 'controller' => 'links', 'action' => 'index',
			'?' => array('menu_id' => $this->data['Menu']['id'])))
		->addCrumb($this->request->data['Link']['title'], $this->here);
	$formUrl = array(
		'controller' => 'links', 'action' => 'edit', 'menu' => $menuId
	);
}

echo $this->Form->create('Link', array('url' => $formUrl));

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#link-basic" data-toggle="tab"><?php echo __('Link'); ?></a></li>
			<li><a href="#link-access" data-toggle="tab"><?php echo __('Access'); ?></a></li>
			<li><a href="#link-misc" data-toggle="tab"><?php echo __('Misc.'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">
			<div id="link-basic" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('menu_id', array(
					'selected' => $menuId,
				));
				echo $this->Form->input('parent_id', array(
					'title' => __('Parent'),
					'options' => $parentLinks,
					'empty' => true,
				));
				$this->Form->inputDefaults(array(
					'class' => 'span10',
				));
				echo $this->Form->input('title', array(
					'label' => false,
					'placeholder' => __('Title'),
				));
				echo $this->Form->input('link', array(
					'label' => false,
					'placeholder' => __('Link'),
				));
				echo $this->Html->link(__('Link to a Node'), Router::url(array(
					'plugin' => 'nodes',
					'controller' => 'nodes',
					'action' => 'index',
					'links' => 1,
				), true) . '?KeepThis=true&TB_iframe=true&height=400&width=600',
					array(
						'class' => 'thickbox',
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
					'label' => false,
					'placeholder' => __('Class'),
				));
				echo $this->Form->input('description', array(
					'label' => false,
					'placeholder' => __('Description'),
				));
				echo $this->Form->input('rel', array(
					'label' => false,
					'placeholder' => __('Rel'),
				));
				echo $this->Form->input('target', array(
					'label' => false,
					'placeholder' => __('Target'),
				));
				echo $this->Form->input('params', array(
					'label' => false,
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
			$this->Form->button(__('Apply'), array('name' => 'apply', 'button' => 'default')) .
			$this->Form->button(__('Save'), array('button' => 'default')) .
			$this->Html->link(__('Cancel'), array('action' => 'index'), array('button' => 'danger')) .
			$this->Form->input('status', array(
				'label' => __('Status'),
				'class' => false,
			)) .
			$this->Html->endBox();
		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
