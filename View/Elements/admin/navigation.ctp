<nav class="navbar-inverse sidebar">
	<div class="navbar-inner">
	<?php
		echo $this->Croogo->adminMenus(CroogoNav::items(), array(
			'htmlAttributes' => array(
				'id' => 'sidebar-menu',
			),
		));
	?>
	</div>
</nav>