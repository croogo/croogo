<?php
declare(strict_types=1);

namespace Croogo\Core\Routing;

use Cake\Core\Configure;
use Cake\Routing\Route\Route;
use Cake\Routing\RouteBuilder as CakeRouteBuilder;
use Croogo\Core\PluginManager as CorePluginManager;

/**
 * @inheritdoc
 *
 * Croogo overrides the connect() method to automatically create localized routes
 */
class RouteBuilder extends CakeRouteBuilder
{

    public function connect($path, $defaults = [], array $options = []): Route
    {
        if (CorePluginManager::isLoaded('Croogo/Translate') && empty($this->_params['prefix'])) {
            $languages = Configure::read('I18n.languages');
            $i18nOptions = array_merge($options, ['lang' => implode('|', $languages)]);
            parent::connect('/{lang}/' . $path, $defaults, $i18nOptions);
        }
        $route = parent::connect($path, $defaults, $options);
        return $route;
    }

}
