<div id="navigation">
	<ul>
		<li>
            <a href="#"><?php __('Content'); ?></a>
            <ul>
                <li><?php echo $html->link(__('List', true), array('plugin' => 0, 'controller' => 'nodes', 'action' => 'index')); ?></li>
                <li><?php echo $html->link(__('Create content', true), array('plugin' => 0, 'controller' => 'nodes', 'action' => 'create')); ?></li>
                <li><?php echo $html->link(__('Content types', true), array('plugin' => 0, 'controller' => 'types', 'action' => 'index')); ?></li>
                <li><?php echo $html->link(__('Taxonomy', true), array('plugin' => 0, 'controller' => 'vocabularies', 'action' => 'index')); ?></li>
                <li><?php echo $html->link(__('Comments', true), array('plugin' => 0, 'controller' => 'comments', 'action' => 'index')); ?></li>
            </ul>
        </li>
		<li>
            <a href="#"><?php __('Menus'); ?></a>
            <ul>
                <li><?php echo $html->link(__('Menus', true), array('plugin' => 0, 'controller' => 'menus', 'action' => 'index')); ?></li>
                <li><?php echo $html->link(__('Add Menu', true), array('plugin' => 0, 'controller' => 'menus', 'action' => 'add')); ?></li>
            </ul>
        </li>
		<li>
            <a href="#"><?php __('Blocks'); ?></a>
            <ul>
                <li><?php echo $html->link(__('Blocks', true), array('plugin' => 0, 'controller' => 'blocks', 'action' => 'index')); ?></li>
                <li><?php echo $html->link(__('Regions', true), array('plugin' => 0, 'controller' => 'regions', 'action' => 'index')); ?></li>
            </ul>
        </li>
        <li>
            <a href="#"><?php __('Media'); ?></a>
            <ul>
                <li><?php echo $html->link(__('Attachments', true), array('plugin' => 0, 'controller' => 'attachments', 'action' => 'index')); ?></li>
                <li><?php echo $html->link(__('File Manager', true), array('plugin' => 0, 'controller' => 'filemanager', 'action' => 'index')); ?></li>
            </ul>
        </li>
        <li>
            <a href="#"><?php __('Contacts'); ?></a>
            <ul>
                <li><?php echo $html->link(__('Contacts', true), array('plugin' => 0, 'controller' => 'contacts', 'action' => 'index')); ?></li>
                <li><?php echo $html->link(__('Messages', true), array('plugin' => 0, 'controller' => 'messages', 'action' => 'index')); ?></li>
            </ul>
        </li>
        <li>
			<a href="#"><?php __('Users'); ?></a>
			<ul>
				<li><?php echo $html->link(__('Users', true), array('plugin' => 0, 'controller' => 'users', 'action' => 'index')); ?></li>
				<li><?php echo $html->link(__('Roles', true), array('plugin' => 0, 'controller' => 'roles', 'action' => 'index')); ?></li>
                <li><?php echo $html->link(__('Permissions', true), array('plugin' => 'acl', 'controller' => 'acl_permissions', 'action' => 'index')); ?></li>
            </ul>
		</li>
        <li>
			<a href="#"><?php __('Extensions'); ?></a>
			<ul>
				<li><?php echo $html->link(__('Themes', true), array('plugin' => 'extensions', 'controller' => 'extensions_themes', 'action' => 'index')); ?></li>
				<li><?php echo $html->link(__('Locales', true), array('plugin' => 'extensions', 'controller' => 'extensions_locales', 'action' => 'index')); ?></li>
				<li><?php echo $html->link(__('Plugins', true), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index')); ?></li>
				<li><?php echo $html->link(__('Hooks', true), array('plugin' => 'extensions', 'controller' => 'extensions_hooks', 'action' => 'index')); ?></li>
			</ul>
		</li>
        <li>
            <a href="#"><?php __('Settings'); ?></a>
            <ul>
                <?php
                    foreach (explode(',', Configure::read('Admin.settings')) AS $prefix) {
                        echo '<li>';
                        echo $html->link(__($prefix, true), array('plugin' => 0, 'controller' => 'settings', 'action' => 'prefix', $prefix));
                        echo '</li>';
                    }
                ?>
                <li><?php echo $html->link(__('Languages', true), array('plugin' => 0, 'controller' => 'languages', 'action' => 'index')); ?></li>
            </ul>
        </li>
        <?php
            foreach (explode(',', Configure::read('Admin.menus')) AS $plugin) {
                if (file_exists(APP.'plugins'.DS.Inflector::underscore($plugin).DS.'views'.DS.'elements'.DS.'admin_menu.ctp')) {
                    echo '<li>';
                    echo $this->element('admin_menu', array('plugin' => Inflector::underscore($plugin)));
                    echo '</li>';
                }
            }
        ?>
	</ul>
</div>