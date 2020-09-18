<?php
declare(strict_types=1);

namespace Croogo\Taxonomy\Controller\Admin;

use Cake\Event\EventInterface;

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

    public function initialize(): void
    {
        parent::initialize();

        $this->Crud->setConfig('actions.index', [
            'displayFields' => $this->Types->displayFields(),
        ]);
    }

    public function implementedEvents(): array
    {
        return parent::implementedEvents() + [
            'Crud.beforePaginate' => 'beforePaginate',
            'Crud.beforeRedirect' => 'beforeCrudRedirect',
            'Crud.beforeFind' => 'beforeCrudFind',
        ];
    }

    public function beforePaginate(EventInterface $event)
    {
        /** @var \Cake\ORM\Query $query */
        $query = $event->getSubject()->query;

        $query->where([
            'plugin IS' => null
        ]);
    }

    public function beforeCrudFind(EventInterface $event)
    {
        /** @var \Cake\ORM\Query $query */
        $query = $event->getSubject()->query;
        $query->contain([
            'Vocabularies',
        ]);
    }

    public function beforeCrudRedirect(EventInterface $event)
    {
        if ($this->redirectToSelf($event)) {
            return;
        }
    }
}
