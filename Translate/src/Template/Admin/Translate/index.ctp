<?php

use Cake\Core\Configure;
use Cake\Utility\Inflector;

$this->extend('Croogo/Core./Common/admin_index');

$plugin = 'Croogo/Nodes';
$controller = 'Nodes';
$modelPath = $this->getRequest()->query('model');
list($plugin, $model) = pluginSplit($modelPath);
$controller = $model;

$crumbLabel = $model == 'Nodes' ? __d('croogo', 'Content') : Inflector::pluralize($model);

$this->Breadcrumbs
    ->add(
        $crumbLabel,
        [
            'plugin' => Inflector::underscore($plugin),
            'controller' => Inflector::underscore($controller),
            'action' => 'index',
        ]
    )
    ->add(
        $record->get($displayField),
        [
            'plugin' => Inflector::underscore($plugin),
            'controller' =>  Inflector::underscore($controller),
            'action' => 'edit',
            $record->id,
        ]
    )
    ->add(__d('croogo', 'Translations'), $this->getRequest()->getRequestTarget());

$this->start('action-buttons');
    $translateButton = $this->Html->link(
        __d('croogo', 'Translate in a new language'),
        [
            'plugin' => 'Croogo/Settings',
            'controller' => 'Languages',
            'action' => 'select',
            '?' => [
                'id' => $record->id,
                'model' => $modelAlias,
            ],
        ],
        [
            'button' => 'outline-secondary',
            'class' => 'btn-sm dropdown-toggle',
            'data-toggle' => 'dropdown',
        ]
    );
    if (!empty($languages)) :
        $out = null;
        foreach ($languages as $languageAlias => $languageDisplay) :
            if ($languageAlias == Configure::read('App.defaultLocale')) :
                continue;
            endif;
            $out .= $this->Croogo->adminAction($languageDisplay, [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Translate',
                'controller' => 'Translate',
                'action' => 'edit',
                '?' => [
                    'id' => $id,
                    'model' => $modelAlias,
                    'locale' => $languageAlias,
                ],
            ], [
                'button' => false,
                'list' => true,
                'class' => 'dropdown-item',
            ]);
        endforeach;
        echo $this->Html->div(
            'btn-group',
            $translateButton .
            $this->Html->tag('ul', $out, ['class' => 'dropdown-menu'])
        );
    endif;
    $this->end();

    if (count($translations->_translations) == 0) :
        echo $this->Html->para(null, __d('croogo', 'No translations available.'));

        return;
    endif;

    $this->append('table-heading');
    $tableHeaders = $this->Html->tableHeaders([
        '',
        __d('croogo', 'Original'),
        __d('croogo', 'Title'),
        __d('croogo', 'Locale'),
        __d('croogo', 'Actions'),
    ]);
    echo $tableHeaders;
    $this->end();

    $this->append('table-body');
    $rows = [];
    foreach ($translations->_translations as $locale => $entity) :
        $actions = [];
        $actions[] = $this->Croogo->adminRowAction('', [
            'action' => 'edit',
            '?' => [
                'id' => $id,
                'model' => $modelAlias,
                'locale' => $locale,
            ],
        ], [
            'icon' => $this->Theme->getIcon('update'),
            'tooltip' => __d('croogo', 'Edit this item'),
        ]);
        $actions[] = $this->Croogo->adminRowAction('', [
            'action' => 'delete',
            $id,
            urlencode($modelAlias),
            $locale,
        ], [
            'icon' => $this->Theme->getIcon('delete'),
            'tooltip' => __d('croogo', 'Remove this item'),
            'method' => 'post',
        ], __d('croogo', 'Are you sure?'));

        $actions = $this->Html->div('item-actions', implode(' ', $actions));
        $rows[] = [
            '',
            $record->title,
            $entity->title,
            $locale,
            $actions,
        ];
    endforeach;

    echo $this->Html->tableCells($rows);
    $this->end();
