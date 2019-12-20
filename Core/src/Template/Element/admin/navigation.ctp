<div class="sidebar-scrollbar">
    <?php

    use Cake\Cache\Cache;
    use Croogo\Core\Nav;

    $cacheKey = 'adminnav_' . $this->Layout->getRoleId() . '_' . $this->getRequest()->getPath() . '_' . md5(serialize($this->getRequest()->getQuery()));
    echo Cache::remember($cacheKey, function () {
        $this->loadHelper('Croogo/Core.AdminMenu');
        return $this->AdminMenu->render(Nav::items());
    }, 'croogo_menus');
    ?>
</div>
