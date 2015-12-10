<?php

namespace Croogo\Blocks\Controller\Admin;

use Croogo\Blocks\Model\Entity\Region;

/**
 * Regions Controller
 *
 * @category Blocks.Controller
 * @package  Croogo.Blocks.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RegionsController extends AppController
{

/**
 * Components
 *
 * @var array
 * @access public
 */
    public $components = [
        'Search.Prg' => [
            'presetForm' => [
                'paramType' => 'querystring',
            ],
            'commonProcess' => [
                'paramType' => 'querystring',
                'filterEmpty' => true,
            ],
        ],
    ];

/**
 * Preset Variables Search
 *
 * @var array
 * @access public
 */
    public $presetVars = true;

/**
 * Admin index
 *
 * @return void
 * @access public
 */
    public function index()
    {
        $this->set('title_for_layout', __d('croogo', 'Region'));
        $this->Prg->commonProcess();
        $searchFields = ['title'];

        $this->paginate = [
            'order' => [
                'title' => 'ASC',
            ],
        ];
        $query = $this->Regions->find('searchable', $this->Prg->parsedParams());
        $this->set('regions', $this->paginate($query));
        $this->set('displayFields', $this->Regions->displayFields());
        $this->set('searchFields', $searchFields);
    }

/**
 * Admin add
 *
 * @return void
 * @access public
 */
    public function add()
    {
        $this->set('title_for_layout', __d('croogo', 'Add Region'));

        $region = $this->Regions->newEntity();
        if (!empty($this->request->data)) {
            $region = $this->Regions->patchEntity($region, $this->request->data);
            $region = $this->Regions->save($region);
            if ($region) {
                $this->Flash->success(__d('croogo', 'The Region has been saved'));
                return $this->Croogo->redirect(['action' => 'edit', $region->id]);
            } else {
                $this->Flash->error(__d('croogo', 'The Region could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('region'));
    }

/**
 * Admin edit
 *
 * @param int$id
 * @return void
 * @access public
 */
    public function edit($id = null)
    {
        $this->set('title_for_layout', __d('croogo', 'Edit Region'));

        if (!$id && empty($this->request->data)) {
            $this->Flash->error(__d('croogo', 'Invalid Region'));
            return $this->redirect(['action' => 'index']);
        }
        if (!empty($this->request->data)) {
            $region = $this->Regions->newEntity($this->request->data);
            if ($this->Regions->save($region)) {
                $this->Flash->success(__d('croogo', 'The Region has been saved'));
                return $this->Croogo->redirect(['action' => 'edit', $id]);
            } else {
                $this->Flash->error(__d('croogo', 'The Region could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $region = $this->Regions->get($id);
            $this->set(compact('region'));
        }
    }

/**
 * Admin delete
 *
 * @param int$id
 * @return void
 * @access public
 */
    public function delete($id = null)
    {
        if (!$id) {
            $this->Flash->error(__d('croogo', 'Invalid id for Region'));
            return $this->redirect(['action' => 'index']);
        }
        $region = new Region(['id' => $id], ['markNew' => false]);
        if ($this->Regions->delete($region)) {
            $this->Flash->success(__d('croogo', 'Region deleted'));
            return $this->redirect(['action' => 'index']);
        }
    }
}
