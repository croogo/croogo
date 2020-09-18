<?php
declare(strict_types=1);

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

    public function implementedEvents(): array
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
        $View = $event->getSubject();
        if ($View->getRequest()->getParam('prefix') !== 'Admin') {
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
            echo $event->getSubject()->Croogo->adminAction($title, [
                'plugin' => 'Croogo/Translate',
                'controller' => 'Translate',
                'action' => 'index',
                'id' => $entity->get('id'),
                'model' => $entity->getSource(),
            ], [
                'icon' => 'translate',
            ]);
        $View->end();
    }
}
