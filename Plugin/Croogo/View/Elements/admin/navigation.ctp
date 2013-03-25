<nav class="navbar-inverse sidebar">
<?php
	echo $this->Croogo->adminMenus(CroogoNav::items(), array(
		'htmlAttributes' => array(
			'id' => 'sidebar-menu',
		),
	));
?>
</nav>