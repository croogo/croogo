<?php
$this->extend('/Common/admin_edit');
$this->set('className', 'translate');

$this->Html
	->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(__d('croogo', 'Translate'), '/' . $this->request->url)
	->addCrumb($modelAlias)
	->addCrumb($this->request->data[$modelAlias][$displayField]);

echo $this->Form->create($modelAlias, array('url' => array(
	'plugin' => 'translate',
	'controller' => 'translate',
	'action' => 'edit',
	$id,
	$modelAlias,
	'locale' => $this->request->params['named']['locale'],
)));
?>
<div class="<?php echo $this->Layout->cssClass('row'); ?>">
	<div class="<?php echo $this->Layout->cssClass('columnLeft'); ?>">
		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Translate'), '#translate-main');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">
			<div id="translate-main" class="tab-pane">
			<?php
				foreach ($fields as $field):
					echo $this->Form->input($modelAlias . '.' . $field);
				endforeach;
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>
	<div class="<?php echo $this->Layout->cssClass('columnRight'); ?>">
		<?php echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
			$this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply', 'class' => 'btn')) .
			$this->Form->button(__d('croogo', 'Save'), array('class' => 'btn btn-primary')) .
			$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index', $this->request->params['pass'][0], $this->request->params['pass'][1]), array('class' => 'cancel btn btn-danger')) .
			$this->Html->endBox(); ?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
