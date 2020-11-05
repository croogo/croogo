<?php
/**
 * @var \App\View\AppView $this
 */
?>
<nav class="navbar-dark bg-black">
    <?php

    use Cake\Cache\Cache;
    use Croogo\Core\Nav;

    $cacheKey = 'adminnav_' . $this->Layout->getRoleId() . '_' . str_replace('/', '-', $this->getRequest()->getPath()) . '_' . md5(serialize($this->getRequest()->getQuery()));
    echo Cache::remember($cacheKey, function () {
        return $this->Croogo->adminMenus(Nav::items(), [
            'htmlAttributes' => [
                'id' => 'sidebar-menu',
            ],
        ]);
    }, 'croogo_menus');
    ?>
</nav>
