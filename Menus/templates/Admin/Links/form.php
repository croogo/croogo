<?php

$this->extend('Croogo/Core./Common/admin_edit');
$this->Croogo->adminScript('Croogo/Menus.admin');

$this->Breadcrumbs->add(__d('croogo', 'Menus'), ['controller' => 'Menus', 'action' => 'index']);

if ($this->getRequest()->getParam('action') == 'add') {
    $this->Breadcrumbs->add(h($menu->title), [
                'action' => 'index',
                '?' => ['menu_id' => $menu->id],
            ])
        ->add(__d('croogo', 'Add'), $this->getRequest()->getRequestTarget());
    $formUrl = [
        'action' => 'add',
        $menu->id,
    ];
}

if ($this->getRequest()->getParam('action') == 'edit') {
    $this->Breadcrumbs->add(h($menu->title), [
            'action' => 'index',
            '?' => ['menu_id' => $menu->id],
        ])
        ->add($link->title, $this->getRequest()->getRequestTarget());
    $formUrl = [
        'action' => 'edit',
        '?' => [
            'menu_id' => $menu->id,
        ],
    ];
}

$this->append('form-start', $this->Form->create($link, [
    'url' => $formUrl,
    'class' => 'protected-form',
]));

//$inputDefaults = $this->Form->getTemplates();
//$inputClass = isset($inputDefaults['class']) ? $inputDefaults['class'] : null;

$this->append('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', 'Link'), '#link-basic');
    echo $this->Croogo->adminTab(__d('croogo', 'Misc.'), '#link-misc');
$this->end();

$this->append('tab-content');

    echo $this->Html->tabStart('link-basic');
        echo $this->Form->control('menu_id', [
            'selected' => $menu->id,
            'class' => 'c-select'
        ]);
        echo $this->Form->control('parent_id', [
            'title' => __d('croogo', 'Parent'),
            'options' => $parentLinks,
            'empty' => __d('croogo', '(no parent)'),
            'class' => 'c-select'
        ]);
        echo $this->Form->control('title', [
            'label' => __d('croogo', 'Title'),
        ]);

        $linkString = (string)$link->link;
        $linkOptions = [];
        if (preg_match('/(plugin:)|(controller:)|(action:)/', $linkString)) :
            $linkKeys = explode('/', $linkString);
            foreach ($linkKeys as $linkKey) :
                $linkOptions[] = [
                    'value' => $linkKey,
                    'text' => urldecode($linkKey),
                    'selected' => true,
                    'data-select2-tag' => "true",
                ];
            endforeach;
        else :
            if (!$link->isNew() && $linkString) :
                $linkOptions[] = [
                    'value' => $linkString,
                    'text' => urldecode($linkString),
                    'selected' => true,
                    'data-select2-tag' => "true",
                ];
            endif;
        endif;

        echo $this->Form->control('link', [
            'label' => __d('croogo', 'Link'),
            'linkChooser' => true,
            'class' => 'no-select2 link-chooser',
            'type' => 'select',
            'multiple' => true,
            'options' => $linkOptions,
        ]);

        echo $this->Html->tabEnd();

        echo $this->Html->tabStart('link-misc');
        echo $this->Form->control('description', [
            'label' => __d('croogo', 'Description'),
        ]);
        echo $this->Form->control('class', [
            'label' => __d('croogo', 'Class'),
        ]);
        echo $this->Form->control('rel', [
            'label' => __d('croogo', 'Rel'),
        ]);
        echo $this->Form->control('target', [
            'label' => __d('croogo', 'Target'),
        ]);
        echo $this->Form->control('params', [
            'label' => __d('croogo', 'Params'),
            'type' => 'stringlist',
        ]);
        echo $this->Html->tabEnd();

        $this->end();

        $this->start('panels');
        echo $this->Html->beginBox(__d('croogo', 'Publishing'));
        echo $this->element('Croogo/Core.admin/buttons', [
            'type' => 'link',
            'cancelUrl' => [
                'action' => 'index',
                'menu_id' => $menu->id,
            ],
        ]);
        echo $this->element('Croogo/Core.admin/publishable');
        echo $this->Html->endBox();

        echo $this->Html->beginBox(__d('croogo', 'Access control'));
        echo $this->Form->control('visibility_roles', [
            'class' => 'c-select',
            'options' => $roles,
            'multiple' => true,
            'label' => false,
        ]);
        echo $this->Html->endBox();
        $this->end();
