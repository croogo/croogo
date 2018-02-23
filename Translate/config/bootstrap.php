<?php

use Cake\Core\Configure;
use Cake\Routing\DispatcherFactory;
use Cake\Event\EventManager;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;
use Croogo\Translate\Middleware\I18nMiddleware;

// Uncomment if to enable locale detection via Accept-Language header
// DispatcherFactory::add('Croogo/Translate.LocaleSelector');
$Languages = TableRegistry::get('Croogo/Settings.Languages');
$languages = $Languages->find('active')->toArray();
Configure::write('I18n.languages', array_keys($languages));
$siteLocale = Configure::read('Site.locale');
I18n::setLocale($siteLocale);

EventManager::instance()->on(
    'Server.buildMiddleware',
    function($event, $stack) use ($siteLocale, $languages) {
        $stack->add(new I18nMiddleware([
            'defaultLanguage' => $siteLocale,
            'languages' => $languages,
        ]));
    }
);
