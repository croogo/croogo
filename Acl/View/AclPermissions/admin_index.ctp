<?php

$this->extend('/Common/admin_index');
$this->name = 'acl_permissions';
$this->Croogo->adminScript('Acl.acl_permissions');

$this->Html
	->addCrumb('', '/admin', array('icon' => $this->Theme->getIcon('home')))
	->addCrumb(__d('croogo', 'Users'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Permissions'), array(
		'plugin' => 'acl', 'controller' => 'acl_permissions',
	));

?>
<?php $this->start('actions'); ?>
<div class="btn-group">
<?php
	echo $this->Html->link(
		__d('croogo', 'Tools') . ' ' . '<span class="caret"></span>',
		'#',
		array(
			'button' => 'default',
			'class' => 'dropdown-toggle',
			'data-toggle' => 'dropdown',
		)
	);

	$generateUrl = array(
		'plugin' => 'acl',
		'controller' => 'acl_actions',
		'action' => 'generate',
		'permissions' => 1
	);
	$out = $this->Croogo->adminAction(__d('croogo', 'Generate'),
		$generateUrl,
		array(
			'button' => false,
			'list' => true,
			'method' => 'post',
			'tooltip' => array(
				'data-title' => __d('croogo', 'Create new actions (no removal)'),
				'data-placement' => 'right',
			),
		)
	);
	$out .= $this->Croogo->adminAction(__d('croogo', 'Synchronize'),
		$generateUrl + array('sync' => 1),
		array(
			'button' => false,
			'list' => true,
			'method' => 'post',
			'tooltip' => array(
				'data-title' => __d('croogo', 'Create new & remove orphaned actions'),
				'data-placement' => 'right',
			),
		)
	);
	echo $this->Html->tag('ul', $out, array('class' => 'dropdown-menu'));
?>
</div>
<?php
	echo $this->Croogo->adminAction(__d('croogo', 'Edit Actions'),
		array('controller' => 'acl_actions', 'action' => 'index', 'permissions' => 1)
	);
?>
<?php $this->end(); ?>

<div class="<?php echo $this->Theme->getCssClass('row'); ?>">
	<div class="<?php echo $this->Theme->getCssClass('columnFull'); ?>">

		<ul id="permissions-tab" class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">
			<?php echo $this->Croogo->adminTabs(); ?>
		</div>

	</div>
</div>

<?php

$this->Js->buffer('AclPermissions.tabSwitcher();');
