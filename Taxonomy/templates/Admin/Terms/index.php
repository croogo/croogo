<?php

if (isset($vocabulary)) :
    $title = __d('croogo', 'Vocabulary: %s', $vocabulary->title);
else :
    $title = __d('croogo', 'Terms');
endif;
$this->assign('title', $title);

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs
    ->add(__d('croogo', 'Content'), [
        'plugin' => 'Croogo/Nodes',
        'controller' => 'Nodes',
        'action' => 'index',
    ])
    ->add(__d('croogo', 'Vocabularies'), [
        'plugin' => 'Croogo/Taxonomy',
        'controller' => 'Vocabularies',
        'action' => 'index',
    ])
    ->add(__d('croogo', 'Terms'), [
        'plugin' => 'Croogo/Taxonomy',
        'controller' => 'Terms',
        'action' => 'index',
    ]);

$this->set('showActions', false);

$this->start('table-heading');
$tableHeaders = $this->Html->tableHeaders([
    __d('croogo', 'Title'),
    __d('croogo', 'Slug'),
    __d('croogo', 'Actions'),
]);
echo $tableHeaders;
$this->end();

$this->append('table-body');
$rows = [];

foreach ($terms as $term) :
    $actions = [];
    $actions[] = $this->Croogo->adminRowActions($term->id);
    $actions[] = $this->Croogo->adminRowAction('', [
        'action' => 'edit', $term->id,
    ], [
        'icon' => $this->Theme->getIcon('update'),
        'escapeTitle' => false,
        'tooltip' => __d('croogo', 'Edit this item'),
    ]);
    $actions[] = $this->Croogo->adminRowAction('', [
        'action' => 'delete', $term->id,
    ], [
        'icon' => $this->Theme->getIcon('delete'),
        'escapeTitle' => false,
        'tooltip' => __d('croogo', 'Remove this item'),
    ], __d('croogo', 'Are you sure?'));
    $actions = $this->Html->div('item-actions', implode(' ', $actions));

    // Title Column
    $titleCol = $term->title;
    if (isset($defaultType['alias'])) {
        $titleCol = $this->Html->link($term->title, [
            'prefix' => false,
            'plugin' => 'Croogo/Nodes',
            'controller' => 'Nodes',
            'action' => 'term',
            'type' => $defaultType->alias,
            'term' => $term->slug,
        ], [
            'target' => '_blank',
        ]);
    }

    if (!empty($term->indent)) :
        $titleCol = str_repeat('&emsp;', $term->indent) . $titleCol;
    endif;

    // Build link list
    $vocabList = [];
    foreach ($term->taxonomies as $taxonomy) :
        $vocabList[] = $taxonomy->vocabulary->title;
    endforeach;
    if (!empty($vocabList)) :
        $titleCol .= sprintf('&nbsp;(%s)', $this->Html->tag('small', implode(', ', $vocabList)));
    endif;

    $rows[] = [
        $titleCol,
        $term->slug,
        $actions,
    ];
endforeach;
echo $this->Html->tableCells($rows);
$this->end();
