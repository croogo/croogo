<?php

namespace Croogo\Meta\Controller\Admin;

use Cake\ORM\TableRegistry;
use Croogo\Meta\Controller\AppController;

/**
 * Meta Controller
 *
 * @category Meta.Controller
 * @package  Croogo.Meta
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MetaController extends AppController
{

/**
 * Preset Variable Search
 *
 * @var array
 * @access public
 */
    public $presetVars = true;

    public function initialize()
    {
        parent::initialize();

        $this->_setupPrg();
    }

/**
 * Admin delete meta
 *
 * @param int$id
 * @return void
 * @access public
 */
    public function deleteMeta($id = null)
    {
        $Meta = TableRegistry::get('Croogo/Meta.Meta');
        $success = false;
        $meta = $Meta->findById($id)->first();
        if ($meta !== null && $Meta->delete($meta)) {
            $success = true;
        } elseif ($meta === null) {
            $success = true;
        }

        $success = ['success' => $success];
        $this->set(compact('success'));
        $this->set('_serialize', 'success');
    }

/**
 * Admin add meta
 *
 * @return void
 * @access public
 */
    public function addMeta()
    {
        $this->viewBuilder()->setLayout('ajax');
    }
}
