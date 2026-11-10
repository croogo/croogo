<?php

$tabContentClass = $this->Theme->getCssClass('tabContentClass');

$this->extend('Croogo/Core./Common/admin_index');

// ?<filemtime>: plugin assets are cached for a day without a cache-buster, and a stale copy
// lacks AclPermissions.search(), which this page calls
$this->Croogo->adminScript($this->Url->script('Croogo/Acl.acl_permissions', ['timestamp' => 'force']));

$this->Breadcrumbs->add(
    __d('croogo', 'Users'),
    ['plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index']
)
    ->add(__d('croogo', 'Permissions'), $this->getRequest()->getUri()->getPath());

$this->append('action-buttons');
$toolsButton = $this->Html->link(__d('croogo', 'Tools'), '#', [
        'button' => 'outline-secondary btn-sm',
        'class' => 'dropdown-toggle',
        'data-toggle' => 'dropdown',
        'escape' => false,
    ]);

// Cake 5 drops URL-array keys that are not route elements: a query string goes under '?',
// or Generate lands on the Actions screen instead of returning here.
// No Synchronize entry: its `sync` key was dropped the same way, so it only ever generated,
// and a working one would delete ACO nodes (with their grants) that this app does not load
// from the database it shares with the legacy app.
$generateUrl = [
    'plugin' => 'Croogo/Acl',
    'controller' => 'Actions',
    'action' => 'generate',
    '?' => ['permissions' => 1],
];
$out = $this->Croogo->adminAction(__d('croogo', 'Generate'), $generateUrl, [
        'button' => false,
        'list' => true,
        'method' => 'post',
        'class' => 'dropdown-item',
        'tooltip' => [
            'data-title' => __d('croogo', 'Create new actions (no removal)'),
            'data-placement' => 'left',
        ],
    ]);
echo $this->Html->div('btn-group', $toolsButton . $this->Html->tag('ul', $out, [
    'class' => 'dropdown-menu dropdown-menu-right',
]));

echo $this->Croogo->adminAction(
    __d('croogo', 'Edit Actions'),
    ['controller' => 'Actions', 'action' => 'index', 'permissions' => 1]
);
$this->end();

$this->Js->buffer('AclPermissions.tabSwitcher();');
$this->Js->buffer('AclPermissions.search();');

?>
<div class="<?= $this->Theme->getCssClass('row') ?>">
    <div class="<?= $this->Theme->getCssClass('columnFull') ?>">

        <form id="permissions-search" class="mb-3 enter-enabled" role="search"
            data-id-label="<?= h(__d('croogo', 'Id')) ?>"
            data-path-label="<?= h(__d('croogo', 'Path')) ?>"
            data-empty="<?= h(__d('croogo', 'No actions match the filter.')) ?>"
            data-count="<?= h(__d('croogo', 'Matching actions: {0}')) ?>"
            data-truncated="<?= h(__d('croogo', 'Showing {0} of {1} matching actions. Narrow the filter to see the rest.')) ?>"
            data-damaged="<?= h(__d('croogo', 'The ACO tree is damaged here (lft/rght do not match parent_id), so a toggle would change another action. Repair the tree first.')) ?>"
            data-error="<?= h(__d('croogo', 'error')) ?>">
            <input type="search" class="form-control form-control-sm w-50" autocomplete="off"
                placeholder="<?= h(__d('croogo', 'Filter by action name or path')) ?>"
                title="<?= h(__d('croogo', 'Every word must appear in the path, for example: orders admin edit')) ?>">
        </form>
        <div id="permissions-search-results" class="hidden"></div>

        <ul id="permissions-tab" class="nav nav-tabs">
        <?php
            echo $this->Croogo->adminTabs();
        ?>
        </ul>

        <div id="permissions-tab-content" class="<?= $tabContentClass ?>">
            <?= $this->Croogo->adminTabs() ?>
        </div>

    </div>
</div>
