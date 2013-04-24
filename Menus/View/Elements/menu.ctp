<div id="menu-<?php echo $menu['Menu']['id']; ?>" class="menu">
<?php
	echo $this->Menus->nestedLinks($menu['threaded'], $options);
?>
</div>