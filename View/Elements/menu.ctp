<div id="menu-<?php echo $menu['Menu']['id']; ?>" class="menu">
<?php
    echo $this->Layout->nestedLinks($menu['threaded'], $options);
?>
</div>