<?php

use Cake\Core\Configure;

$this->extend('Croogo/Core./Common/admin_index');

$this->assign('title', __d('croogo', 'Themes'));

$this->Breadcrumbs->add(__d('croogo', 'Extensions'),
        ['plugin' => 'Croogo/Extensions', 'controller' => 'Plugins', 'action' => 'index'])
    ->add(__d('croogo', 'Themes'), $this->getRequest()->getUri()->getPath());

$this->start('action-buttons');
echo $this->Croogo->adminAction(__d('croogo', 'Upload'), ['action' => 'add']);
$this->end() ?>

<div class="extensions-themes card-columns" style="column-count: 2">
<?php
    foreach ($themesData as $themeAlias => $theme):
        echo $this->element('admin/theme-preview', ['theme' => $theme]);
    endforeach;
?>
</div>
