<?php

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs
    ->add(__d('croogo', 'Settings'), array(
        'prefix' => 'admin',
        'plugin' => 'Croogo/Settings',
        'controller' => 'Settings',
        'action' => 'index',
    ));
if (!empty($this->request->params['named']['p'])) {
    $this->Breadcrumbs->add($this->request->params['named']['p']);
}
$this->start('table-heading');
    $tableHeaders = $this->Html->tableHeaders(array(
        $this->Paginator->sort('id', __d('croogo', 'Id')),
        $this->Paginator->sort('key', __d('croogo', 'Key')),
        $this->Paginator->sort('value', __d('croogo', 'Value')),
        $this->Paginator->sort('editable', __d('croogo', 'Editable')),
        __d('croogo', 'Actions'),
    ));
    echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
    $rows = array();
    foreach ($settings as $setting):
        $actions = array();
        $actions[] = $this->Croogo->adminRowAction('',
            array('controller' => 'Settings', 'action' => 'moveup', $setting->id),
            array('icon' =>$this->Theme->getIcon('move-up'), 'tooltip' => __d('croogo', 'Move up'))
        );
        $actions[] = $this->Croogo->adminRowAction('',
            array('controller' => 'Settings', 'action' => 'movedown', $setting->id),
            array('icon' => $this->Theme->getIcon('move-down'), 'tooltip' => __d('croogo', 'Move down'))
        );
        $actions[] = $this->Croogo->adminRowAction('',
            array('controller' => 'Settings', 'action' => 'edit', $setting->id),
            array('icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item'))
        );
        $actions[] = $this->Croogo->adminRowActions($setting->id);
        $actions[] = $this->Croogo->adminRowAction('',
            array('controller' => 'Settings', 'action' => 'delete', $setting->id),
            array('icon' => $this->Theme->getIcon('delete'), 'tooltip' => __d('croogo', 'Remove this item')),
            __d('croogo', 'Are you sure?'));

        $key = $setting->key;
        $keyE = explode('.', $key);
        $keyPrefix = $keyE['0'];
        if (isset($keyE['1'])) {
            $keyTitle = '.' . $keyE['1'];
        } else {
            $keyTitle = '';
        }
        $actions = $this->Html->div('item-actions', implode(' ', $actions));
        $rows[] = array(
            $setting->id,
            $this->Html->link($keyPrefix, array('controller' => 'Settings', 'action' => 'index', '?' => array('key' => $keyPrefix))) . $keyTitle,
            $this->Text->truncate($setting->value, 20),
            $this->Html->status($setting->editable),
            $actions,
        );
    endforeach;

    echo $this->Html->tableCells($rows);
$this->end();
