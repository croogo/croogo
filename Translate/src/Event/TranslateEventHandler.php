<?php

namespace Croogo\Translate\Event;

use Cake\Datasource\EntityInterface;
use Cake\Event\EventListenerInterface;
use Croogo\Translate\Translations;

/**
 * TranslateEventHandler
 *
 * @package  Croogo.Translate.Event
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TranslateEventHandler implements EventListenerInterface
{

    public function implementedEvents()
    {
        return [
            'Croogo.bootstrapComplete' => [
                'callable' => 'onCroogoBootstrapComplete',
            ],
            'View.beforeRender' => [
                'callable' => 'onBeforeRender',
            ],
        ];
    }

    public function onCroogoBootstrapComplete($event)
    {
        Translations::translateModels();
    }

    public function onBeforeRender($event)
    {
        $View = $event->subject;
        if ($View->request->param('prefix') !== 'admin') {
            return;
        }
        if (empty($View->viewVars['viewVar'])) {
            return;
        }
        $viewVar = $View->viewVars['viewVar'];
        $entity = $View->viewVars[$viewVar];
        if (!$entity instanceof EntityInterface) {
            return;
        }
        if ($entity->isNew()) {
            return;
        }
        $title = __d('croogo', 'Translate');
        $View->append('action-buttons');
            echo $event->subject->Croogo->adminAction($title, [
                'plugin' => 'Croogo/Translate',
                'controller' => 'Translate',
                'action' => 'index',
                'id' => $entity->get('id'),
                'model' => $entity->source(),
            ], [
                'icon' => 'translate',
            ]);
        $View->end();
    }

}
