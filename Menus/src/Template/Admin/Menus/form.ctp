<?php

use Croogo\Croogo\CroogoStatus;

$this->extend('Croogo/Croogo./Common/admin_edit');

$this->CroogoHtml
	->addCrumb('', '/admin', ['icon' => 'home'])
	->addCrumb(__d('croogo', 'Menus'), ['action' => 'index']);

if ($this->request->params['action'] == 'edit') {
	$this->CroogoHtml->addCrumb($menu->title, '/' . $this->request->url);

	$this->assign('title', __d('croogo', 'Edit Menu'));
}

if ($this->request->params['action'] == 'add') {
	$this->CroogoHtml->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);

	$this->assign('title', __d('croogo', 'Add Menu'));
}

echo $this->CroogoForm->create($menu);

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Menu'), '#menu-basic');
			echo $this->Croogo->adminTab(__d('croogo', 'Misc.'), '#menu-misc');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">

			<div id="menu-basic" class="tab-pane">
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

			<div id="menu-misc" class="tab-pane">
			<?php
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
		echo $this->CroogoHtml->beginBox('Publishing') .
			$this->CroogoForm->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
			$this->CroogoForm->button(__d('croogo', 'Save'), array('button' => 'success')) .
			$this->CroogoHtml->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('button' => 'danger')) .
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

		$this->Croogo->adminBoxes();
		?>
	</div>

</div>
<?php echo $this->CroogoForm->end(); ?>
