<?php

/**
 * Translations
 *
 * @package  Croogo.Translate.Lib
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
namespace Croogo\Translate;

use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Croogo\Core\Croogo;
use \Cake\Log\Log;

class Translations
{

/**
 * Read configured Translate.models and hook the appropriate behaviors
 */
    public static function translateModels()
    {
        $path ='admin:true/plugin:Croogo%2fTranslate/controller:Translate/action:index/?id=:id&model={{model}}';
        foreach (Configure::read('Translate.models') as $encoded => $config) {
            $model = base64_decode($encoded);
            Croogo::hookBehavior($model, 'Translate', $config);
            $action = str_replace('.', '.Admin/', $model . '/index');
            $url = str_replace('{{model}}', $model, $path);
            Croogo::hookAdminRowAction($action,
                __d('croogo', 'Translate'),
                [
                $url => [
                    'title' => false,
                    'options' => [
                        'icon' => 'translate',
                        'data-title' => __d('croogo', 'Translate'),
                    ],
                ]]
            );
        }
    }

}
