<?php

namespace Croogo\Taxonomy\Controller\Admin;

use Cake\Cache\Cache;
use Cake\Event\Event;

/**
 * Vocabularies Controller
 *
 * @category Taxonomy.Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 *
 * @property \Croogo\Taxonomy\Model\Table\VocabulariesTable Vocabularies
 */
class VocabulariesController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->Crud->setConfig('actions.moveUp', [
            'className' => 'Croogo/Core.Admin/MoveUp'
        ]);
        $this->Crud->setConfig('actions.moveDown', [
            'className' => 'Croogo/Core.Admin/MoveDown'
        ]);
    }

    public function beforeCrudRender(Event $event)
    {
        if (!isset($event->getSubject()->entity)) {
            return;
        }

        $entity = $event->getSubject()->entity;

        $this->set('types', $this->Vocabularies->Types->pluginTypes($entity->plugin));
    }

    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.afterSave' => 'afterCrudSave',
            'Crud.beforeFind' => 'beforeCrudFind',
            'Crud.beforeRender' => 'beforeCrudRender'
        ];
    }

    public function beforeCrudFind(Event $event)
    {
        return $event->getSubject()->query->contain('Types');
    }

    public function afterCrudSave(Event $event)
    {
        Cache::clearAll();
    }
}
