<div class="sidebar-scrollbar">
    <?php

    use Cake\Cache\Cache;
    use Croogo\Core\Nav;

    $cacheKey = 'adminnav_' . $this->Layout->getRoleId() . '_' . $this->getRequest()->getPath() . '_' . md5(serialize($this->getRequest()->getQuery()));
    echo Cache::remember($cacheKey, function () {
        return $this->Croogo->adminMenus(Nav::items(), [
            'htmlAttributes' => [
                'id' => 'sidebar-menu',
                'class' => 'nav sidebar-inner',
            ],
        ]);
    }, 'croogo_menus');
    ?>
</div>
