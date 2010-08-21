<div id="nav">
    <ul class="sf-menu">
        <li>
            <a href="<?php echo $html->url('/admin'); ?>"><span class="ui-icon ui-icon-home"></span><?php __('Dashboard'); ?></a>
        </li>

        <li>
            <a href="<?php echo $html->url(array('plugin' => null, 'controller' => 'nodes', 'action' => 'index')); ?>"><span class="ui-icon ui-icon-document"></span><?php __('Content'); ?></a>
            <ul>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-document"></span>' . __('List', true), array('plugin' => null, 'controller' => 'nodes', 'action' => 'index'), array('escape' => false)); ?></li>
                <li>
                    <?php echo $html->link('<span class="ui-icon ui-icon-plus"></span>' . __('Create content', true), array('plugin' => null, 'controller' => 'nodes', 'action' => 'create'), array('escape' => false)); ?>
                    <ul>
                        <?php foreach ($types_for_admin_layout AS $t) { ?>
                        <li><?php echo $html->link('<span class="ui-icon ui-icon-bullet"></span>' . $t['Type']['title'], array('plugin' => null, 'controller' => 'nodes', 'action' => 'add', $t['Type']['alias']), array('escape' => false)); ?></li>
                        <?php } ?>
                    </ul>
                </li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-document-b"></span>' . __('Content types', true), array('plugin' => null, 'controller' => 'types', 'action' => 'index'), array('escape' => false)); ?></li>
                <li>
                    <?php echo $html->link('<span class="ui-icon ui-icon-tag"></span>' . __('Taxonomy', true), array('plugin' => null, 'controller' => 'vocabularies', 'action' => 'index'), array('escape' => false)); ?>
                    <ul>
                        <li><?php echo $html->link('<span class="ui-icon ui-icon-tag"></span>' . __('List', true), array('plugin' => null, 'controller' => 'vocabularies', 'action' => 'index'), array('escape' => false)); ?></li>
                        <li><?php echo $html->link('<span class="ui-icon ui-icon-plus"></span>' . __('Add new', true), array('plugin' => null, 'controller' => 'vocabularies', 'action' => 'add'), array('class' => 'separator', 'escape' => false)); ?></li>
                        <?php foreach ($vocabularies_for_admin_layout AS $v) { ?>
                        <li><?php echo $html->link('<span class="ui-icon ui-icon-bullet"></span>' . $v['Vocabulary']['title'], array('plugin' => null, 'controller' => 'terms', 'action' => 'index', $v['Vocabulary']['id']), array('escape' => false)); ?></li>
                        <?php } ?>
                    </ul>
                </li>
                <li>
                    <?php echo $html->link('<span class="ui-icon ui-icon-comment"></span>' . __('Comments', true), array('plugin' => null, 'controller' => 'comments', 'action' => 'index'), array('escape' => false)); ?>
                    <ul>
                        <li><?php echo $html->link('<span class="ui-icon ui-icon-check"></span>' . __('Published', true), array('plugin' => null, 'controller' => 'comments', 'action' => 'index', 'filter' => 'status:1;'), array('escape' => false)); ?></li>
                        <li><?php echo $html->link('<span class="ui-icon ui-icon-radio-off"></span>' . __('Approval', true), array('plugin' => null, 'controller' => 'comments', 'action' => 'index', 'filter' => 'status:0;'), array('escape' => false)); ?></li>
                    </ul>
                </li>
            </ul>
        </li>

        <li>
            <a href="<?php echo $html->url(array('plugin' => null, 'controller' => 'menus', 'action' => 'index')); ?>"><span class="ui-icon ui-icon-link"></span><?php __('Menus'); ?></a>
            <ul>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-link"></span>' . __('Menus', true), array('plugin' => null, 'controller' => 'menus', 'action' => 'index'), array('escape' => false)); ?></li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-plus"></span>' . __('Add new', true), array('plugin' => null, 'controller' => 'menus', 'action' => 'add'), array('class' => 'separator', 'escape' => false)); ?></li>
                <?php foreach ($menus_for_admin_layout AS $m) { ?>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-bullet"></span>' . $m['Menu']['title'], array('plugin' => null, 'controller' => 'links', 'action' => 'index', $m['Menu']['id']), array('escape' => false)); ?></li>
                <?php } ?>
            </ul>
        </li>

        <li>
            <a href="<?php echo $html->url(array('plugin' => null, 'controller' => 'blocks', 'action' => 'index')); ?>"><span class="ui-icon ui-icon-copy"></span><?php __('Blocks'); ?></a>
            <ul>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-copy"></span>' . __('Blocks', true), array('plugin' => null, 'controller' => 'blocks', 'action' => 'index'), array('escape' => false)); ?></li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-bullet"></span>' . __('Regions', true), array('plugin' => null, 'controller' => 'regions', 'action' => 'index'), array('escape' => false)); ?></li>
            </ul>
        </li>

        <li>
            <a href="<?php echo $html->url(array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index')); ?>"><span class="ui-icon ui-icon-gear"></span><?php __('Extensions'); ?></a>
            <ul>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-image"></span>' . __('Themes', true), array('plugin' => 'extensions', 'controller' => 'extensions_themes', 'action' => 'index'), array('escape' => false)); ?></li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-script"></span>' . __('Locales', true), array('plugin' => 'extensions', 'controller' => 'extensions_locales', 'action' => 'index'), array('escape' => false)); ?></li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-gear"></span>' . __('Plugins', true), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'), array('escape' => false, 'class' => Configure::read('Admin.menus') ? 'separator' : '', 'escape' => false)); ?></li>
                <?php
                if (Configure::read('Admin.menus')) {
                    foreach (array_keys(Configure::read('Admin.menus')) AS $p) {
                        if (file_exists(APP.'plugins'.DS.$p.DS.'views'.DS.'elements'.DS.'admin_menu.ctp')) {
                            echo '<li>';
                            echo $this->element('admin_menu', array('plugin' => $p));
                            echo '</li>';
                        }
                    }
                }
                ?>
            </ul>
        </li>

        <li>
            <a href="<?php echo $html->url(array('plugin' => null, 'controller' => 'attachments', 'action' => 'index')); ?>"><span class="ui-icon ui-icon-video"></span><?php __('Media'); ?></a>
            <ul>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-image"></span>' . __('Attachments', true), array('plugin' => null, 'controller' => 'attachments', 'action' => 'index'), array('escape' => false)); ?></li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-folder-collapsed"></span>' . __('File Manager', true), array('plugin' => null, 'controller' => 'filemanager', 'action' => 'index'), array('escape' => false)); ?></li>
            </ul>
        </li>

        <li>
            <a href="<?php echo $html->url(array('plugin' => null, 'controller' => 'contacts', 'action' => 'index')); ?>"><span class="ui-icon ui-icon-contact"></span><?php __('Contacts'); ?></a>
            <ul>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-contact"></span>' . __('Contacts', true), array('plugin' => null, 'controller' => 'contacts', 'action' => 'index'), array('escape' => false)); ?></li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-mail-closed"></span>' . __('Messages', true), array('plugin' => null, 'controller' => 'messages', 'action' => 'index'), array('escape' => false)); ?></li>
            </ul>
        </li>

        <li>
            <a href="<?php echo $html->url(array('plugin' => null, 'controller' => 'users', 'action' => 'index')); ?>"><span class="ui-icon ui-icon-person"></span><?php __('Users'); ?></a>
            <ul>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-person"></span>' . __('Users', true), array('plugin' => null, 'controller' => 'users', 'action' => 'index'), array('escape' => false)); ?></li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-star"></span>' . __('Roles', true), array('plugin' => null, 'controller' => 'roles', 'action' => 'index'), array('escape' => false)); ?></li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-circle-check"></span>' . __('Permissions', true), array('plugin' => 'acl', 'controller' => 'acl_permissions', 'action' => 'index'), array('escape' => false)); ?></li>
            </ul>
        </li>

        <li>
            <a href="<?php echo $html->url(array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Site')); ?>" class="last"><span class="ui-icon ui-icon-wrench"></span><?php __('Settings'); ?></a>
            <ul>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-wrench"></span>' . __('Site', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Site'), array('escape' => false)); ?></li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-info"></span>' . __('Meta', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Meta'), array('escape' => false)); ?></li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-document"></span>' . __('Reading', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Reading'), array('escape' => false)); ?></li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-pencil"></span>' . __('Writing', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Writing'), array('escape' => false)); ?></li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-comment"></span>' . __('Comment', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Comment'), array('escape' => false)); ?></li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-transferthick-e-w"></span>' . __('Service', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Service'), array('escape' => false)); ?></li>
                <li><?php echo $html->link('<span class="ui-icon ui-icon-script"></span>' . __('Languages', true), array('plugin' => null, 'controller' => 'languages', 'action' => 'index'), array('escape' => false)); ?></li>
            </ul>
        </li>
    </ul>
</div>