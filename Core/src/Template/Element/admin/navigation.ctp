<nav class="navbar-inverse sidebar">
    <div class="navbar-inner">
        <?php
        use Cake\Cache\Cache;
        use Croogo\Core\Nav;

        $cacheKey = 'adminnav_' . $this->Layout->getRoleId();
        $navItems = Cache::read($cacheKey, 'croogo_menus');
        if ($navItems === false) {
            $navItems = $this->Croogo->adminMenus(Nav::items(), array(
                'htmlAttributes' => array(
                    'id' => 'sidebar-menu',
                ),
            ));
            Cache::write($cacheKey, $navItems, 'croogo_menus');
        }
        echo $navItems;
        ?>
    </div>
</nav>
