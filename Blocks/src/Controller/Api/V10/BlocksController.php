<?php
declare(strict_types=1);

namespace Croogo\Blocks\Controller\Api\V10;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Croogo\Core\Controller\Api\AppController;

/**
 * Blocks Controller
 */
class BlocksController extends AppController
{

    public function index()
    {
        $this->Crud->on('afterPaginate', function(Event $event) {
            $View = $this->viewBuilder()->build();
            $View->loadHelper('Croogo/Core.Html');
            $View->loadHelper('Croogo/Core.Layout');
            $View->loadHelper('Croogo/Blocks.Regions');
            $View->loadHelper('Croogo/Nodes.Nodes');
            $View->loadHelper('Croogo/Menus.Menus');
            $View->loadHelper('Croogo/Taxonomy.Taxonomies');

            $limit = Configure::read('Reading.nodes_per_page');

            $Nodes = TableRegistry::getTableLocator()->get('Croogo/Nodes.Nodes');
            $nodesForLayout['recent_posts'] = $Nodes->find()
                ->orderDesc('publish_start')
                ->limit($limit);
            $View->set(compact('nodesForLayout'));

            $Menus = TableRegistry::getTableLocator()->get('Croogo/Menus.Menus');
            $Links = TableRegistry::getTableLocator()->get('Croogo/Menus.Links');
            foreach ($Menus->find() as $menu) {
                $menusForLayout[$menu->alias] = $menu;
                $menusForLayout[$menu->alias]['threaded'] = $Links->find('threaded')
                    ->where([
                        'menu_id' => $menu->id,
                        'status' => 1,
                    ])
                    ->orderAsc('lft')
                    ->toArray();
            }
            $View->set(compact('menusForLayout'));

            $Taxonomies = TableRegistry::getTableLocator()->get('Croogo/Taxonomy.Taxonomies');
            $types = $Taxonomies->Vocabularies->Types->find('all');
            foreach ($types as $type) {
                $typesForLayout[$type->alias] = $type;
            }
            $View->set(compact('typesForLayout'));

            $Vocabularies = TableRegistry::getTableLocator()->get('Croogo/Taxonomy.Vocabularies');
            $vocabularies = $Vocabularies->find();
            foreach ($vocabularies as $vocabulary) {
                $threaded = $Taxonomies->find('threaded')
                    ->where([
                        'Taxonomies.vocabulary_id' => $vocabulary->id,
                    ])
                    ->orderAsc('lft')
                    ->contain(['Terms']);
                $vocabulariesForLayout[$vocabulary->alias]['vocabulary'] = $vocabulary;
                $vocabulariesForLayout[$vocabulary->alias]['threaded'] = $threaded;
            }
            $View->set(compact('vocabulariesForLayout'));

            foreach ($event->getSubject()->entities as $entity) {
                $entity->rendered = $View->Regions->block($entity);
            }
        });
        return $this->Crud->execute();
    }

    public function view()
    {
        return $this->Crud->execute();
    }

}
