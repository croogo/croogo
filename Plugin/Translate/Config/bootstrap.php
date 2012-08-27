<?php

function getModelAlias($modelAlias = null) {
    if ($modelAlias == null) {
        throw new Exception('modelAlias cannot be null');
    }
    $alias = explode('.', $modelAlias);
    return $alias[count($alias) - 1];
}

/**
 * Configuration
 *
 */
Configure::write('Translate.models', array(
    'Node' => array(
        'title' => 'titleTranslation',
        'excerpt' => 'excerptTranslation',
        'body' => 'bodyTranslation',
    ),
    'Block' => array(
        'title' => 'titleTranslation',
        'body' => 'bodyTranslation',
    ),
    'Link' => array(
        'title' => 'titleTranslation',
        'description' => 'descriptionTranslation',
    )
));

/**
 * 
 * Do not edit below this line unless you know what you are doing.
 *
 */
foreach (Configure::read('Translate.models') as $translateModel => $fields) {
    Croogo::hookBehavior(getModelAlias($translateModel), 'CroogoTranslate', $fields);
    Croogo::hookAdminRowAction(Inflector::pluralize(getModelAlias($translateModel)) . '/admin_index', __('Translate'), 'plugin:translate/controller:translate/action:index/:id/' . $translateModel);
}