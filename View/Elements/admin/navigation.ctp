<div id="nav">
<?php

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
