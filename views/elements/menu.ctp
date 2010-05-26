<div id="menu-<?php echo $menu['Menu']['id']; ?>" class="menu">
<?php
    echo $layout->nestedLinks($menu['threaded'], $options);
?>
</div>