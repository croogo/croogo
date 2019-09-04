<?php

use Cake\Core\Configure;

$this->extend('Croogo/Core./Common/admin_index');

$this->assign('title', __d('croogo', 'Locales'));

$this->Breadcrumbs
    ->add(__d('croogo', 'Extensions'), array('plugin' => 'Croogo/Extensions', 'controller' => 'Plugins', 'action' => 'index'))
    ->add(__d('croogo', 'Locales'), $this->request->getUri()->getPath());

$this->append('action-buttons');
    echo $this->Croogo->adminAction(__d('croogo', 'Upload'),
        array('action' => 'add')
    );
$this->end();

$this->start('table-heading');
    $tableHeaders = $this->Html->tableHeaders(array(
        '',
        __d('croogo', 'Locale'),
        __d('croogo', 'Name'),
        __d('croogo', 'Default'),
        __d('croogo', 'Actions'),
    ));
    echo $tableHeaders;
$this->end();

$this->append('table-body');
    $rows = array();
    $vendorDir = ROOT . DS . 'vendor' . DS . 'croogo' . DS . 'locale' . DS;
    $siteLocale = Configure::read('Site.locale');
    foreach ($locales as $locale => $data):
        $actions = array();

        if ($locale == $siteLocale) {
            $status = $this->Html->status(1);
            $actions[] = $this->Croogo->adminRowAction('',
                array('action' => 'deactivate', $locale),
                [
                    'icon' => $this->Theme->getIcon('power-off'),
                    'escapeTitle' => false,
                    'tooltip' => __d('croogo', 'Deactivate'),
                    'method' => 'post'
                ]
            );
        } else {
            $status = $this->Html->status(0);
            $actions[] = $this->Croogo->adminRowAction('',
                array('action' => 'activate', $locale),
                [
                    'icon' => $this->Theme->getIcon('power-on'),
                    'escapeTitle' => false,
                    'tooltip' => __d('croogo', 'Activate'),
                    'method' => 'post'
                ]
            );
        }

        $actions[] = $this->Croogo->adminRowAction('',
            array('action' => 'edit', $locale),
            [
                'icon' => $this->Theme->getIcon('update'),
                'escapeTitle' => false,
                'tooltip' => __d('croogo', 'Edit this item')
            ]
        );

        if (strpos($data['path'], $vendorDir) !== 0):
            $actions[] = $this->Croogo->adminRowAction('',
                ['action' => 'delete', $locale],
                [
                    'icon' => $this->Theme->getIcon('delete'),
                    'tooltip' => __d('croogo', 'Remove this item'),
                ],
                __d('croogo', 'Are you sure?')
            );
        endif;
        $actions = $this->Html->div('item-actions', implode(' ', $actions));

        $rows[] = array(
            '',
            $locale,
            $data['name'],
            $status,
            $actions,
        );
    endforeach;

    usort($rows, function($a, $b) {
        return strcmp($a[3], $b[3]);
    });

    echo $this->Html->tableCells($rows);
$this->end();
?>
