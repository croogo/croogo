<?php

namespace Croogo\Meta\Controller\Admin;

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
    public function delete_meta($id = null)
    {
        $Meta = ClassRegistry::init('Meta.Meta');
        $success = false;
        if ($id != null && $Meta->delete($id)) {
            $success = true;
        } else {
            if (!$Meta->exists($id)) {
                $success = true;
            }
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
    public function add_meta()
    {
        $this->layout = 'ajax';
    }
}
