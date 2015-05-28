<?php

use Croogo\Croogo\CroogoStatus;

$this->extend('Croogo/Croogo./Common/admin_edit');
$this->Croogo->adminScript('Croogo/Menus.admin');

$this->CroogoHtml
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Menus'), array('controller' => 'Menus', 'action' => 'index'));

if ($this->request->params['action'] == 'add') {
	$this->CroogoHtml
		->addCrumb($menu->title, array(
			'action' => 'index',
			'?' => array('menu_id' => $menu->id))
		)
		->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
	$formUrl = array(
		'action' => 'add', $menu->id
	);
}

if ($this->request->params['action'] == 'edit') {
	$this->CroogoHtml
		->addCrumb($menu->title, array(
			'action' => 'index',
			'?' => array('menu_id' => $menu->id)))
		->addCrumb($link->title, '/' . $this->request->url);
	$formUrl = array(
		'action' => 'edit',
		'?' => array(
			'menu_id' => $menuId,
		),
	);
}

echo $this->CroogoForm->create($link, array(
	'url' => $formUrl,
	'class' => 'protected-form',
));

$linkChooserUrl = $this->Url->build(array(
	'action' => 'linkChooser',
));

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
				echo $this->CroogoForm->input('id');
				echo $this->CroogoForm->input('menu_id', array(
					'selected' => $menuId,
				));
				echo $this->CroogoForm->input('parent_id', array(
					'title' => __d('croogo', 'Parent'),
					'options' => $parentLinks,
					'empty' => true,
				));
				$this->CroogoForm->templates(array(
					'class' => 'span10',
				));
				echo $this->CroogoForm->input('title', array(
					'label' => __d('croogo', 'Title'),
				));

				echo $this->CroogoForm->input('link', array(
					'label' => __d('croogo', 'Link'),
					'div' => 'input text required input-append',
					'after' => $this->CroogoHtml->link('', '#link_choosers', array(
						'button' => 'default',
						'icon' => array('link'),
						'iconSize' => 'small',
						'data-title' => 'Link Chooser',
						'data-toggle' => 'modal',
						'data-remote' => $linkChooserUrl,
					)),
				));
			?>
			</div>

			<div id="link-access" class="tab-pane">
			<?php
				echo $this->CroogoForm->input('visibility_roles', array(
					'class' => false,
					'options' => $roles,
					'multiple' => true
				));
			?>
			</div>

			<div id="link-misc" class="tab-pane">
			<?php
				echo $this->CroogoForm->input('class', array(
					'label' => __d('croogo', 'Class'),
					'class' => 'span10 class',
				));
				echo $this->CroogoForm->input('description', array(
					'label' => __d('croogo', 'Description'),
				));
				echo $this->CroogoForm->input('rel', array(
					'label' => __d('croogo', 'Rel'),
				));
				echo $this->CroogoForm->input('target', array(
					'label' => __d('croogo', 'Target'),
				));
				echo $this->CroogoForm->input('params', array(
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
			$this->CroogoHtml->link(__d('croogo', 'Cancel'), array('action' => 'index', '?' => array('menu_id' => $menuId)), array('button' => 'danger')) .
			$this->CroogoForm->input('status', array(
				'type' => 'radio',
				'legend' => false,
				'class' => false,
				'default' => CroogoStatus::UNPUBLISHED,
				'options' => $this->Croogo->statuses(),
			)) .
			$this->CroogoHtml->div('input-daterange',
				$this->CroogoForm->input('publish_start', array(
					'label' => __d('croogo', 'Publish Start'),
					'type' => 'text',
				)) .
				$this->CroogoForm->input('publish_end', array(
					'label' => __d('croogo', 'Publish End'),
					'type' => 'text',
				))
			) .
			$this->CroogoHtml->endBox();
		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->CroogoForm->end(); ?>
<?php
echo $this->element('Croogo/Croogo.admin/modal', array(
	'id' => 'link_choosers',
	'class' => 'hide',
	'title' => __d('croogo', 'Choose Link'),
));
