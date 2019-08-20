<nav class="navbar-dark bg-black">
    <?php
    use Cake\Cache\Cache;
    use Croogo\Core\Nav;

    $cacheKey = 'adminnav_' . $this->Layout->getRoleId() . '_' . $this->request->getPath() . '_' . md5(serialize($this->request->getAttribute('query')));
    echo Cache::remember($cacheKey, function () {
        return $this->Croogo->adminMenus(Nav::items(), [
            'htmlAttributes' => [
                'id' => 'sidebar-menu',
            ],
        ]);
    }, 'croogo_menus');
    ?>
</nav>
