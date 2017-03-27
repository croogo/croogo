<?php

$this->extend('/Common/admin_index');

$this->Breadcrumbs
    ->add(__d('croogo', 'Settings'), [
        'plugin' => 'Croogo/Settings',
        'controller' => 'Settings',
        'action' => 'index',
    ])
    ->add(__d('croogo', 'Languages'), [
        'plugin' => 'Croogo/Settings',
        'controller' => 'Languages',
        'action' => 'index'
    ]);

$this->append('main');
    $html = null;
    foreach ($languages as $language):
        $title = $language->title . ' (' . $language->native . ')';
        $link = $this->Html->link($title, array(
            'plugin' => 'Croogo/Translate',
            'controller' => 'Translate',
            'action' => 'edit',
            '?' => [
                'id' => $id,
                'model' => $modelAlias,
                'locale' => $language->alias,
            ],
        ));
        $html .= '<li>' . $link . '</li>';
    endforeach;
    echo $this->Html->div(
        $this->Theme->getCssClass('columnFull'),
        $this->Html->tag('ul', $html)
    );
$this->end();
