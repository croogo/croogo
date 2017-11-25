<?php

namespace Croogo\Taxonomy\Controller\Admin;

use Cake\Event\Event;

/**
 * Types Controller
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 *
 * @property \Croogo\Taxonomy\Model\Table\TypesTable Types
 */
class TypesController extends AppController
{
    public $modelClass = 'Croogo/Taxonomy.Types';

    public function initialize()
    {
        parent::initialize();

        $this->Crud->config('actions.index', [
            'displayFields' => $this->Types->displayFields(),
        ]);
    }

    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforePaginate' => 'beforePaginate',
            'Crud.beforeRedirect' => 'beforeCrudRedirect',
            'Crud.beforeFind' => 'beforeCrudFind',
        ];
    }

    public function beforePaginate(Event $event)
    {
        /** @var \Cake\ORM\Query $query */
        $query = $event->subject()->query;

        $query->where([
            'plugin IS' => null
        ]);
    }

    public function beforeCrudFind(Event $event)
    {
        /** @var \Cake\ORM\Query $query */
        $query = $event->subject()->query;
        $query->contain([
            'Vocabularies',
        ]);
    }

    public function beforeCrudRedirect(Event $event)
    {
        if ($this->redirectToSelf($event)) {
            return;
        }
    }

}
