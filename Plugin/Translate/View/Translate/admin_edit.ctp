<?php
$this->extend('/Common/admin_edit');
$this->set('className', 'translate');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Translate'), $this->here)
	->addCrumb($modelAlias)
	->addCrumb($this->data[$modelAlias]['title']);

echo $this->Form->create($modelAlias, array('url' => array(
	'plugin' => 'translate',
	'controller' => 'translate',
	'action' => 'edit',
	$id,
	$modelAlias,
	'locale' => $this->params['named']['locale'],
)));
?>
<div class="row-fluid">
	<div class="span8">
		<ul class="nav nav-tabs">
			<li><a href="#translate-main" data-toggle="tab"><?php echo __('Translate'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">
			<div id="translate-main" class="tab-pane">
			<?php
				$this->Form->inputDefaults(array(
					'class' => 'span10',
				));
				foreach ($fields as $field):
					echo $this->Form->input($modelAlias . '.' . $field);
				endforeach;
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>
	<div class="span4">
		<?php echo $this->Html->beginBox(__('Publishing')) .
			$this->Form->button(__('Save'), array('class' => 'btn')) .
			$this->Html->link(__('Cancel'), array('action' => 'index', $this->request->params['pass'][0], $this->request->params['pass'][1]), array('class' => 'cancel btn btn-danger')) .
			$this->Html->endBox(); ?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
