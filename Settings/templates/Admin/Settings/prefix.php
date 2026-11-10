<?php

use Cake\Utility\Inflector;

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs->add(
    __d('croogo', 'Settings'),
    ['plugin' => 'Croogo/Settings', 'controller' => 'Settings', 'action' => 'index']
)
    ->add($prefix, $this->getRequest()->getRequestTarget());

$this->append('action-buttons');
echo $this->Croogo->adminAction('Purchase Statistics', ['action' => 'prefix', 'Purchase Statistics']);
echo $this->Croogo->adminAction('Purchase Limits', ['action' => 'prefix', 'Purchase Limits']);
echo $this->Croogo->adminAction('Ebay UK', ['action' => 'prefix', 'EbayUK']);
echo $this->Croogo->adminAction('Ebay DE', ['action' => 'prefix', 'EbayEU']);
echo $this->Croogo->adminAction('Ebay FR', ['action' => 'prefix', 'EbayFR']);
echo $this->Croogo->adminAction('Ebay IT', ['action' => 'prefix', 'EbayIT']);
echo $this->Croogo->adminAction('Ebay ES', ['action' => 'prefix', 'EbayES']);
echo $this->Croogo->adminAction('Ebay AT', ['action' => 'prefix', 'EbayAT']);
echo $this->Croogo->adminAction('Allegro', ['action' => 'prefix', 'Allegro']);
echo $this->Croogo->adminAction('Currency', ['action' => 'prefix', 'Currency Exchange']);
$this->end();

$this->assign('form-start', $this->Form->create(null, [
    'class' => 'protected-form',
    'type' => 'file',
]));

$this->append('tab-heading');
echo $this->Croogo->adminTab($prefix, '#settings-main');
$this->end();

$this->append('tab-content');
echo $this->Html->tabStart('settings-main');
foreach ($settings as $setting) :
    if (!empty($setting->params['tab'])) {
        continue;
    }
    $keyE = explode('.', $setting->key);
    $keyTitle = Inflector::humanize($keyE['1']);

    $label = ($setting->title != null) ? $setting->title : $keyTitle;

    echo $this->SettingsForm->input($setting, $label);
endforeach;

if( $keyE['0'] == 'EbayUK' ) {
    echo $this->Html->link(
            'Authorize Ebay UK',
            [
                'plugin' => 'EbayUK',
                'controller' => 'Settings',
                'action' => 'index'
            ],
            [
                'class' => 'btn btn-primary'
            ]
        );
}

if( $keyE['0'] == 'EbayEU' ) {
    echo $this->Html->link(
        'Authorize Ebay EU',
        [
            'plugin' => 'EbayUK',
            'controller' => 'Settings',
            'action' => 'eu'
        ],
        [
            'class' => 'btn btn-primary'
        ]
    );
}

if( $keyE['0'] == 'EbayFR' ) {
    echo $this->Html->link(
        'Authorize Ebay FR',
        [
            'plugin' => 'EbayUK',
            'controller' => 'Settings',
            'action' => 'fr'
        ],
        [
            'class' => 'btn btn-primary'
        ]
    );
}

if( $keyE['0'] == 'EbayIT' ) {
    echo $this->Html->link(
        'Authorize Ebay IT',
        [
            'plugin' => 'EbayUK',
            'controller' => 'Settings',
            'action' => 'it'
        ],
        [
            'class' => 'btn btn-primary'
        ]
    );
}


if( $keyE['0'] == 'EbayES' ) {
    echo $this->Html->link(
        'Authorize Ebay ES',
        [
            'plugin' => 'EbayUK',
            'controller' => 'Settings',
            'action' => 'es'
        ],
        [
            'class' => 'btn btn-primary'
        ]
    );
}

if( $keyE['0'] == 'Allegro' ) {
    echo $this->Html->link(
        'Authorize Allegro',
        [
            'plugin' => 'AllegroPL',
            'controller' => 'Settings',
            'action' => 'index'
        ],
        [
            'class' => 'btn btn-primary'
        ]
    ) . '&nbsp';

    echo $this->Html->link(
        'Download all products from Allegro',
        [
            'plugin' => 'AllegroPL',
            'controller' => 'Products',
            'action' => 'getAllProducts'
        ],
        [
            'class' => 'btn btn-primary'
        ]
    );
}

echo $this->Html->tabEnd();
$this->end();

$this->start('buttons');
    echo $this->Html->beginBox(__d('croogo', 'Publishing'));
    echo $this->element('Croogo/Core.admin/buttons', ['applyText' => false]);
    echo $this->Html->endBox();
$this->end();
