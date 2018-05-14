<?php

use Cake\Core\Configure;
use Cake\Utility\Inflector;

$this->extend('Croogo/Core./Common/admin_index');

$plugin = 'Croogo/Nodes'; $controller = 'Nodes';
$modelPath = $this->request->query('model');
list($plugin, $model) = pluginSplit($modelPath);
$controller = $model;

$crumbLabel = $model == 'Nodes' ? __d('croogo', 'Content') : Inflector::pluralize($model);

$this->Breadcrumbs
    ->add(
        $crumbLabel,
        array(
            'plugin' => Inflector::underscore($plugin),
            'controller' => Inflector::underscore($controller),
            'action' => 'index',
        )
    )
    ->add(
        $record->get($displayField),
        array(
            'plugin' => Inflector::underscore($plugin),
            'controller' =>  Inflector::underscore($controller),
            'action' => 'edit',
            $record->id,
        )
    )
    ->add(__d('croogo', 'Translations'), $this->request->getRequestTarget());

$this->start('action-buttons');
    $translateButton = $this->Html->link(
        __d('croogo', 'Translate in a new language'),
        array(
            'plugin' => 'Croogo/Settings',
            'controller' => 'Languages',
            'action' => 'select',
            '?' => [
                'id' => $record->id,
                'model' => $modelAlias,
            ],
        ),
        array(
            'button' => 'secondary',
            'class' => 'dropdown-toggle',
            'data-toggle' => 'dropdown',
        )
    );
    if (!empty($languages)):
        $out = null;
        foreach ($languages as $languageAlias => $languageDisplay):
            if ($languageAlias == Configure::read('App.defaultLocale')):
                continue;
            endif;
            $out .= $this->Croogo->adminAction($languageDisplay, array(
                'prefix' => 'admin',
                'plugin' => 'Croogo/Translate',
                'controller' => 'Translate',
                'action' => 'edit',
                '?' => [
                    'id' => $id,
                    'model' => $modelAlias,
                    'locale' => $languageAlias,
                ],
            ), array(
                'button' => false,
                'list' => true,
                'class' => 'dropdown-item',
            ));
        endforeach;
        echo $this->Html->div('btn-group',
            $translateButton .
            $this->Html->tag('ul', $out, array('class' => 'dropdown-menu'))
        );
    endif;
$this->end();

if (count($translations) == 0):
    echo $this->Html->para(null, __d('croogo', 'No translations available.'));
    return;
endif;

$this->append('table-heading');
    $tableHeaders = $this->Html->tableHeaders(array(
        '',
        __d('croogo', 'Original'),
        __d('croogo', 'Title'),
        __d('croogo', 'Locale'),
        __d('croogo', 'Actions'),
    ));
    echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
    $rows = array();
    foreach ($translations->_translations as $locale => $entity):
        $actions = array();
        $actions[] = $this->Croogo->adminRowAction('', array(
            'action' => 'edit',
            '?' => [
                'id' => $id,
                'model' => $modelAlias,
                'locale' => $locale,
            ],
        ), array(
            'icon' => $this->Theme->getIcon('update'),
            'tooltip' => __d('croogo', 'Edit this item'),
        ));
        $actions[] = $this->Croogo->adminRowAction('', array(
            'action' => 'delete',
            $id,
            urlencode($modelAlias),
            $locale,
        ), array(
            'icon' => $this->Theme->getIcon('delete'),
            'tooltip' => __d('croogo', 'Remove this item'),
            'method' => 'post',
        ), __d('croogo', 'Are you sure?'));

        $actions = $this->Html->div('item-actions', implode(' ', $actions));
        $rows[] = array(
            '',
            $record->title,
            $entity->title,
            $locale,
            $actions,
        );
    endforeach;

    echo $this->Html->tableCells($rows);
$this->end();
