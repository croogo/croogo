<?php

use Cake\Core\Configure;
use Cake\Routing\DispatcherFactory;
use Cake\Event\EventManager;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;
use Croogo\Translate\Middleware\I18nMiddleware;

DispatcherFactory::add('Croogo/Translate.LocaleSelector');
$Languages = TableRegistry::get('Croogo/Settings.Languages');
$languages = $Languages->find('active')->toArray();
Configure::write('I18n.languages', array_keys($languages));
I18n::locale(Configure::read('App.defaultLocale'));

EventManager::instance()->on(
    'Server.buildMiddleware',
    function($event, $stack) use ($languages) {
        $stack->add(new I18nMiddleware([
            'defaultLanguage' => 'en',
            'languages' => $languages,
        ]));
    }
);
