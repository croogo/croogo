<nav class="navbar-inverse sidebar">
    <div class="navbar-inner">
        <?php
        use Cake\Cache\Cache;
        use Croogo\Core\Nav;

        $cacheKey = 'adminnav_' . $this->Layout->getRoleId() . '_' . $this->request->url . '_' . md5(serialize($this->request->query));
        echo Cache::remember($cacheKey, function () {
            return $this->Croogo->adminMenus(Nav::items(), [
                'htmlAttributes' => [
                    'id' => 'sidebar-menu',
                ],
            ]);
        }, 'croogo_menus');
        ?>
    </div>
</nav>
