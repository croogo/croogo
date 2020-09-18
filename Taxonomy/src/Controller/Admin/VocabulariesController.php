<?php
declare(strict_types=1);

namespace Croogo\Taxonomy\Controller\Admin;

use Cake\Cache\Cache;
use Cake\Event\EventInterface;

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
    public function initialize(): void
    {
        parent::initialize();

        $this->Crud->setConfig('actions.moveUp', [
            'className' => 'Croogo/Core.Admin/MoveUp'
        ]);
        $this->Crud->setConfig('actions.moveDown', [
            'className' => 'Croogo/Core.Admin/MoveDown'
        ]);
    }

    public function beforeCrudRender(EventInterface $event)
    {
        if (!isset($event->getSubject()->entity)) {
            return;
        }

        $entity = $event->getSubject()->entity;

        $this->set('types', $this->Vocabularies->Types->pluginTypes($entity->plugin));
    }

    public function implementedEvents(): array
    {
        return parent::implementedEvents() + [
            'Crud.afterSave' => 'afterCrudSave',
            'Crud.beforeFind' => 'beforeCrudFind',
            'Crud.beforeRender' => 'beforeCrudRender'
        ];
    }

    public function beforeCrudFind(EventInterface $event)
    {
        return $event->getSubject()->query->contain('Types');
    }

    public function afterCrudSave(EventInterface $event)
    {
        Cache::clearAll();
    }
}
