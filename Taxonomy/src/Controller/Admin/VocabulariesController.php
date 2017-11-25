<?php

namespace Croogo\Taxonomy\Controller\Admin;
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

        $this->Crud->config('actions.moveUp', [
            'className' => 'Croogo/Core.Admin/MoveUp'
        ]);
        $this->Crud->config('actions.moveDown', [
            'className' => 'Croogo/Core.Admin/MoveDown'
        ]);
    }

    public function beforeCrudRender(Event $event)
    {
        if (!isset($event->subject()->entity)) {
            return;
        }

        $entity = $event->subject()->entity;

        $this->set('types', $this->Vocabularies->Types->pluginTypes($entity->plugin));
    }

    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforeRender' => 'beforeCrudRender'
        ];
    }
}
