<?php
/**
 * Sidebar Navigation Menu
 * 
 * TODO: Detectar qual Model/Controller foi acessado e destacar no menu.
 * TODO: Deixar o submenu aberto, caso esteja no Model/Controller correspondente
*/
?>

<div id="croogo-sidebar" class="sidebar-nav">
	<div class="accordion" id="croogo-navigation">

		<div class="accordion-group <?php echo ($this->name == 'Settings' && $this->action == 'admin_dashboard') ? ' active' : ''?>">
			<div class="accordion-heading">
				<?php
                    echo html_entity_decode(
                        $this->Html->link('<i class="icon-home icon-large"></i>'.'Dashboard','/admin',array('class'=>'accordion-toggle'))
                    );
                ?>
			</div>
		</div>

		<div class="accordion-group">
			<div class="accordion-heading">
				<?php
                    echo html_entity_decode(
                        $this->Html->link('<i class="icon-caret-right icon-large"></i>'.__('Content', true), '#Submenu-Nodes' ,array('class'=>'accordion-toggle','data-toggle'=>'collapse','data-parent'=>'#croogo-navigation'))
                    );
                ?>
			</div>
			<div id="Submenu-Nodes" class="accordion-body collapse">
				<div class="accordion-inner">
					<ul class="submenu nav nav-list">
					<?php 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('List', true), array('plugin' => null, 'controller' => 'nodes', 'action' => 'index'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Create', true), array('plugin' => null, 'controller' => 'nodes', 'action' => 'create'))).'</li>';

							foreach ($types_for_admin_layout AS $t) {
								echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.$t['Type']['title'], array('plugin' => null, 'controller' => 'nodes', 'action' => 'add', $t['Type']['alias']))).'</li>';
                            }
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Content types', true), array('plugin' => null, 'controller' => 'types', 'action' => 'index'))).'</li>';
					?>
					</ul>
				</div>
			</div>
		</div>

		<div class="accordion-group">
			<div class="accordion-heading">
				<?php
                    echo html_entity_decode(
                        $this->Html->link('<i class="icon-caret-right icon-large"></i>'.__('Taxonomy', true), '#Submenu-Vocabularies' ,array('class'=>'accordion-toggle','data-toggle'=>'collapse','data-parent'=>'#croogo-navigation'))
                    );
                ?>
			</div>
			<div id="Submenu-Vocabularies" class="accordion-body collapse in">
				<div class="accordion-inner">
					<ul class="submenu nav nav-list">
					<?php 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('List', true), array('plugin' => null, 'controller' => 'vocabularies', 'action' => 'index'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Create', true), array('plugin' => null, 'controller' => 'vocabularies', 'action' => 'add'))).'</li>';

							foreach ($vocabularies_for_admin_layout AS $v) {
								echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.$v['Vocabulary']['title'], array('plugin' => null, 'controller' => 'terms', 'action' => 'index', $v['Vocabulary']['id']))).'</li>';
                            }
					?>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="accordion-group">
			<div class="accordion-heading">
				<?php
                    echo html_entity_decode(
                        $this->Html->link('<i class="icon-caret-right icon-large"></i>'.__('Comments', true), '#Submenu-Comments' ,array('class'=>'accordion-toggle','data-toggle'=>'collapse','data-parent'=>'#croogo-navigation'))
                    );
                ?>
			</div>
			<div id="Submenu-Comments" class="accordion-body collapse in">
				<div class="accordion-inner">
					<ul class="submenu nav nav-list">
					<?php 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Published', true), array('plugin' => null, 'controller' => 'comments', 'action' => 'index', 'filter' => 'status:1;'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Approval', true), array('plugin' => null, 'controller' => 'comments', 'action' => 'index', 'filter' => 'status:0;'))).'</li>';
					?>
					</ul>
				</div>
			</div>
		</div>

		<div class="accordion-group">
			<div class="accordion-heading">
				<?php
                    echo html_entity_decode(
                        $this->Html->link('<i class="icon-caret-right icon-large"></i>'.__('Menus', true), '#Submenu-Menus' ,array('class'=>'accordion-toggle','data-toggle'=>'collapse','data-parent'=>'#croogo-navigation'))
                    );
                ?>
			</div>
			<div id="Submenu-Menus" class="accordion-body collapse in">
				<div class="accordion-inner">
					<ul class="submenu nav nav-list">
					<?php 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Menus', true), array('plugin' => null, 'controller' => 'menus', 'action' => 'index'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Add new', true), array('plugin' => null, 'controller' => 'menus', 'action' => 'add'))).'</li>';

							foreach ($menus_for_admin_layout AS $m) {
								echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.$m['Menu']['title'], array('plugin' => null, 'controller' => 'links', 'action' => 'index', $m['Menu']['id']))).'</li>';
                            }
					?>
					</ul>
				</div>
			</div>
		</div>

		<div class="accordion-group">
			<div class="accordion-heading">
				<?php
                    echo html_entity_decode(
                        $this->Html->link('<i class="icon-caret-right icon-large"></i>'.__('Blocks', true), '#Submenu-Blocks' ,array('class'=>'accordion-toggle','data-toggle'=>'collapse','data-parent'=>'#croogo-navigation'))
                    );
                ?>
			</div>
			<div id="Submenu-Blocks" class="accordion-body collapse in">
				<div class="accordion-inner">
					<ul class="submenu nav nav-list">
					<?php 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Blocks', true), array('plugin' => null, 'controller' => 'blocks', 'action' => 'index'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Regions', true), array('plugin' => null, 'controller' => 'regions', 'action' => 'index'))).'</li>';
					?>
					</ul>
				</div>
			</div>
		</div>

		<div class="accordion-group">
			<div class="accordion-heading">
				<?php
                    echo html_entity_decode(
                        $this->Html->link('<i class="icon-caret-right icon-large"></i>'.__('Extensions', true), '#Submenu-ExtensionsPlugins' ,array('class'=>'accordion-toggle','data-toggle'=>'collapse','data-parent'=>'#croogo-navigation'))
                    );
                ?>
			</div>
			<div id="Submenu-ExtensionsPlugins" class="accordion-body collapse in">
				<div class="accordion-inner">
					<ul class="submenu nav nav-list">
					<?php 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Themes', true), array('plugin' => 'extensions', 'controller' => 'extensions_themes', 'action' => 'index'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Locales', true), array('plugin' => 'extensions', 'controller' => 'extensions_locales', 'action' => 'index'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Plugins', true), array('plugin' => 'extensions', 'controller' => 'extensions_plugins', 'action' => 'index'), array('class' => Configure::read('Admin.menus') ? 'separator' : '', 'escape' => false))).'</li>';

						if (Configure::read('Admin.menus')) {
		                    foreach (array_keys(Configure::read('Admin.menus')) AS $p) {
		                        echo '<li>';
		                        echo $this->element('admin_menu', array('plugin' => $p));
		                        echo '</li>';
		                    }
		                }
					?>
					</ul>
				</div>
			</div>
		</div>

		<div class="accordion-group">
			<div class="accordion-heading">
				<?php
                    echo html_entity_decode(
                        $this->Html->link('<i class="icon-caret-right icon-large"></i>'.__('Media', true), '#Submenu-Attachments' ,array('class'=>'accordion-toggle','data-toggle'=>'collapse','data-parent'=>'#croogo-navigation'))
                    );
                ?>
			</div>
			<div id="Submenu-Attachments" class="accordion-body collapse in">
				<div class="accordion-inner">
					<ul class="submenu nav nav-list">
					<?php 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Attachments', true), array('plugin' => null, 'controller' => 'attachments', 'action' => 'index'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('File Manager', true), array('plugin' => null, 'controller' => 'filemanager', 'action' => 'index'))).'</li>'; 
					?>
					</ul>
				</div>
			</div>
		</div>

		<div class="accordion-group">
			<div class="accordion-heading">
				<?php
                    echo html_entity_decode(
                        $this->Html->link('<i class="icon-caret-right icon-large"></i>'.__('Contacts', true), '#Submenu-Contacts' ,array('class'=>'accordion-toggle','data-toggle'=>'collapse','data-parent'=>'#croogo-navigation'))
                    );
                ?>
			</div>
			<div id="Submenu-Contacts" class="accordion-body collapse in">
				<div class="accordion-inner">
					<ul class="submenu nav nav-list">
					<?php 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Contacts', true), array('plugin' => null, 'controller' => 'contacts', 'action' => 'index'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Messages', true), array('plugin' => null, 'controller' => 'messages', 'action' => 'index'))).'</li>'; 
					?>
					</ul>
				</div>
			</div>
		</div>

		<div class="accordion-group">
			<div class="accordion-heading">
				<?php
                    echo html_entity_decode(
                        $this->Html->link('<i class="icon-caret-right icon-large"></i>'.__('Users', true), '#Submenu-Users' ,array('class'=>'accordion-toggle','data-toggle'=>'collapse','data-parent'=>'#croogo-navigation'))
                    );
                ?>
			</div>
			<div id="Submenu-Users" class="accordion-body collapse in">
				<div class="accordion-inner">
					<ul class="submenu nav nav-list">
					<?php 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Users', true), array('plugin' => null, 'controller' => 'users', 'action' => 'index'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Roles', true), array('plugin' => null, 'controller' => 'roles', 'action' => 'index'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Permissions', true), array('plugin' => 'acl', 'controller' => 'acl_permissions', 'action' => 'index'))).'</li>'; 
					?>
					</ul>
				</div>
			</div>
		</div>

		<div class="accordion-group">
			<div class="accordion-heading">
				<?php
                    echo html_entity_decode(
                        $this->Html->link('<i class="icon-caret-right icon-large"></i>'.__('Settings', true), '#Submenu-Settings' ,array('class'=>'accordion-toggle','data-toggle'=>'collapse','data-parent'=>'#croogo-navigation'))
                    );
                ?>
			</div>
			<div id="Submenu-Settings" class="accordion-body collapse in">
				<div class="accordion-inner">
					<ul class="submenu nav nav-list">
					<?php 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Site', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Site'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Meta', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Meta'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Reading', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Reading'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Writing', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Writing'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Comment', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Comment'))).'</li>'; 
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Service', true), array('plugin' => null, 'controller' => 'settings', 'action' => 'prefix', 'Service'))).'</li>'; 
						
						echo '<li>'.html_entity_decode($this->Html->link('<i class="icon-pencil"></i>'.__('Languages', true), array('plugin' => null, 'controller' => 'languages', 'action' => 'index'))).'</li>'; 
					?>
					</ul>
				</div>
			</div>
		</div>

	</div>
</div><!--/#croogo-sidebar -->