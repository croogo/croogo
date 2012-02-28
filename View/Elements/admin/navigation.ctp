<div id="nav">
<?php

foreach ($types_for_admin_layout as $t):
	CroogoNav::add('content.children.create.children.' . $t['Type']['alias'], array(
		'title' => $t['Type']['title'],
		'url' => array(
			'plugin' => false,
			'admin' => true,
			'controller' => 'nodes',
			'action' => 'add',
			$t['Type']['alias'],
			),
		));
endforeach;

foreach ($vocabularies_for_admin_layout as $v):
	$weight = 9999 + $v['Vocabulary']['weight'];
	CroogoNav::add('content.children.taxonomy.children.' . $v['Vocabulary']['alias'], array(
		'title' => $v['Vocabulary']['title'],
		'url' => array(
			'plugin' => false,
			'admin' => true,
			'controller' => 'terms',
			'action' => 'index',
			$v['Vocabulary']['id'],
			),
		'weight' => $weight,
		));
endforeach;

foreach ($menus_for_admin_layout as $m):
	$weight = 9999 + $m['Menu']['weight'];
	CroogoNav::add('menus.children.' . $m['Menu']['alias'], array(
		'title' => $m['Menu']['title'],
		'url' => array(
			'plugin' => false,
			'admin' => true,
			'controller' => 'links',
			'action' => 'index',
			$m['Menu']['id'],
			),
		'weight' => $weight,
		));
endforeach;

echo $this->Layout->adminMenus(CroogoNav::items());
?>
</div>
