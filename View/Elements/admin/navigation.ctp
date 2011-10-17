<div id="nav">
    <ul class="sf-menu">
        <li>
            <?php echo $this->Html->link(__('Content'), array('plugin' => null, 'controller' => 'nodes', 'action' => 'index')); ?>
            <ul>
                <li><?php echo $this->Html->link(__('List'), array('plugin' => null, 'controller' => 'nodes', 'action' => 'index')); ?></li>
                <li>
                    <?php echo $this->Html->link(__('Create'), array('plugin' => null, 'controller' => 'nodes', 'action' => 'create')); ?>
                    <ul>
                        <?php foreach ($types_for_admin_layout AS $t) { ?>
                        <li><?php echo $this->Html->link($t['Type']['title'], array('plugin' => null, 'controller' => 'nodes', 'action' => 'add', $t['Type']['alias'])); ?></li>
                        <?php } ?>
                    </ul>
                </li>
                <li><?php echo $this->Html->link(__('Content types'), array('plugin' => null, 'controller' => 'types', 'action' => 'index')); ?></li>
                <li>
                    <?php echo $this->Html->link(__('Taxonomy'), array('plugin' => null, 'controller' => 'vocabularies', 'action' => 'index')); ?>
                    <ul>
                        <li><?php echo $this->Html->link(__('List'), array('plugin' => null, 'controller' => 'vocabularies', 'action' => 'index')); ?></li>
                        <li><?php echo $this->Html->link(__('Add new'), array('plugin' => null, 'controller' => 'vocabularies', 'action' => 'add'), array('class' => 'separator')); ?></li>
                        <?php foreach ($vocabularies_for_admin_layout AS $v) { ?>
                        <li><?php echo $this->Html->link($v['Vocabulary']['title'], array('plugin' => null, 'controller' => 'terms', 'action' => 'index', $v['Vocabulary']['id'])); ?></li>
                        <?php } ?>
                    </ul>
                </li>
                <li>
                    <?php echo $this->Html->link(__('Comments'), array('plugin' => null, 'controller' => 'comments', 'action' => 'index')); ?>
                    <ul>
                        <li><?php echo $this->Html->link(__('Published'), array('plugin' => null, 'controller' => 'comments', 'action' => 'index', 'filter' => 'status:1;')); ?></li>
                        <li><?php echo $this->Html->link(__('Approval'), array('plugin' => null, 'controller' => 'comments', 'action' => 'index', 'filter' => 'status:0;')); ?></li>
                    </ul>
                </li>
            </ul>
        </li>

        <li>
            <?php echo $this->Html->link(__('Menus'), array('plugin' => null, 'controller' => 'menus', 'action' => 'index')); ?>
            <ul>
                <li><?php echo $this->Html->link(__('Menus'), array('plugin' => null, 'controller' => 'menus', 'action' => 'index')); ?></li>
                <li><?php echo $this->Html->link(__('Add new'), array('plugin' => null, 'controller' => 'menus', 'action' => 'add'), array('class' => 'separator')); ?></li>
                <?php foreach ($menus_for_admin_layout AS $m) { ?>
                <li><?php echo $this->Html->link($m['Menu']['title'], array('plugin' => null, 'controller' => 'links', 'action' => 'index', $m['Menu']['id'])); ?></li>
                <?php } ?>
            </ul>
        </li>

        <li>
            <?php echo $this->Html->link(__('Blocks'), array('plugin' => null, 'controller' => 'blocks', 'action' => 'index')); ?>
            <ul>
                <li><?php echo $this->Html->link(__('Blocks'), array('plugin' => null, 'controller' => 'blocks', 'action' => 'index')); ?></li>
                <li><?php echo $this->Html->link(__('Regions'), array('plugin' => null, 'controller' => 'regions', 'action' => 'index')); ?></li>
            </ul>
        </li>

        <li>
            <?php echo $this->Html->link(__('Extensions'), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index')); ?>
            <ul>
                <li><?php echo $this->Html->link(__('Themes'), array('plugin' => 'extensions', 'controller' => 'extensions_themes', 'action' => 'index')); ?></li>
                <li><?php echo $this->Html->link(__('Locales'), array('plugin' => 'extensions', 'controller' => 'extensions_locales', 'action' => 'index')); ?></li>
                <li><?php echo $this->Html->link(__('Plugins'), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'), array('class' => Configure::read('Admin.menus') ? 'separator' : '', 'escape' => false)); ?></li>
                <?php
                if (Configure::read('Admin.menus')) {
                    foreach (array_keys(Configure::read('Admin.menus')) AS $p) {
                        echo '<li>';
                        echo $this->element('admin_menu', array(), array('plugin' => $p));
                        echo '</li>';
                    }
                }
                ?>
            </ul>
        </li>

        <li>
            <?php echo $this->Html->link(__('Media'), array('plugin' => null, 'controller' => 'attachments', 'action' => 'index')); ?>
            <ul>
                <li><?php echo $this->Html->link(__('Attachments'), array('plugin' => null, 'controller' => 'attachments', 'action' => 'index')); ?></li>
                <li><?php echo $this->Html->link(__('File Manager'), array('plugin' => null, 'controller' => 'filemanager', 'action' => 'index')); ?></li>
            </ul>
        </li>

        <li>
            <?php echo $this->Html->link(__('Contacts'), array('plugin' => null, 'controller' => 'contacts', 'action' => 'index')); ?>
            <ul>
                <li><?php echo $this->Html->link(__('Contacts'), array('plugin' => null, 'controller' => 'contacts', 'action' => 'index')); ?></li>
                <li><?php echo $this->Html->link(__('Messages'), array('plugin' => null, 'controller' => 'messages', 'action' => 'index')); ?></li>
            </ul>
        </li>

        <li>
            <?php echo $this->Html->link(__('Users'), array('plugin' => null, 'controller' => 'users', 'action' => 'index')); ?>
            <ul>
                <li><?php echo $this->Html->link(__('Users'), array('plugin' => null, 'controller' => 'users', 'action' => 'index')); ?></li>
                <li><?php echo $this->Html->link(__('Roles'), array('plugin' => null, 'controller' => 'roles', 'action' => 'index')); ?></li>
                <li><?php echo $this->Html->link(__('Permissions'), array('plugin' => 'acl', 'controller' => 'acl_permissions', 'action' => 'index')); ?></li>
            </ul>
        </li>

        <li>
            <?php echo $this->Html->link(__('Settings'), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Site')); ?>
            <ul>
                <li><?php echo $this->Html->link(__('Site'), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Site')); ?></li>
                <li><?php echo $this->Html->link(__('Meta'), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Meta')); ?></li>
                <li><?php echo $this->Html->link(__('Reading'), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Reading')); ?></li>
                <li><?php echo $this->Html->link(__('Writing'), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Writing')); ?></li>
                <li><?php echo $this->Html->link(__('Comment'), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Comment')); ?></li>
                <li><?php echo $this->Html->link(__('Service'), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Service')); ?></li>
                <li><?php echo $this->Html->link(__('Languages'), array('plugin' => null, 'controller' => 'languages', 'action' => 'index')); ?></li>
            </ul>
        </li>
    </ul>
</div>