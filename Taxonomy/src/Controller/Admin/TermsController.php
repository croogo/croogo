<?php

namespace Croogo\Taxonomy\Controller\Admin;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Network\Response;
use Croogo\Taxonomy\Model\Table\TermsTable;

/**
 * Terms Controller
 *
 * @property TermsTable Terms
 * @category Taxonomy.Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TermsController extends AppController
{

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Terms';

    protected $_redirectUrl = [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Taxonomy',
        'controller' => 'Vocabularies',
        'action' => 'index',
    ];

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    public $uses = ['Taxonomy.Term'];

    /**
     * Initialize
     */
    public function initialize()
    {
        parent::initialize();

        $this->Crud->config('actions.add', [
            'className' => 'Croogo/Taxonomy.Admin/TermAdd',
        ]);
        $this->Crud->config('actions.edit', [
            'className' => 'Croogo/Taxonomy.Admin/TermEdit',
        ]);
    }

    /**
     * beforeFilter
     *
     * @return void
     * @access public
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->vocabularyId = null;
        if (isset($this->request->params['named']['vocabulary'])) {
            $this->vocabularyId = $this->request->params['named']['vocabulary'];
        }
        $this->set('vocabulary', $this->vocabularyId);
    }

    /**
     * Admin index
     *
     * @param int $vocabularyId
     * @access public
     */
    public function index($vocabularyId = null)
    {
        $vocabularyId = $this->request->query('vocabulary_id');
        $response = $this->_ensureVocabularyIdExists($vocabularyId);
        if ($response instanceof Response) {
            return $response;
        }

        $vocabulary = $this->Terms->Vocabularies->get($vocabularyId, [
            'contain' => [
                'Types',
            ],
        ]);
        $defaultType = $this->__getDefaultType($vocabulary);

        $terms = $this->Terms->find()
            ->innerJoinWith('Taxonomies', function($q) use ($vocabularyId) {
                return $q->where([
                    'vocabulary_id' => $vocabularyId,
                ]);
            })
            ->orderAsc('lft');
        $this->set(compact('vocabulary', 'terms', 'defaultType'));

        if (isset($this->request->params['named']['links']) || isset($this->request->query['chooser'])) {
            $this->render('chooser');
        }
    }

    /**
     * Admin delete
     *
     * @param int $id
     * @param int $vocabularyId
     * @return void
     * @access public
     */
    public function delete($id = null, $vocabularyId = null)
    {
        $redirectUrl = ['action' => 'index', 'vocabulary_id' => $vocabularyId];
        $this->_ensureVocabularyIdExists($vocabularyId, $redirectUrl);
        $this->_ensureTermExists($id, $redirectUrl);
        $taxonomyId = $this->Terms->Taxonomies->termInVocabulary($id, $vocabularyId);
        $this->_ensureVocabularyIdExists($vocabularyId, $redirectUrl);

        if ($this->Terms->remove($id, $vocabularyId)) {
            $messageFlash = __d('croogo', 'Term deleted');
            $flashMethod = 'success';
        } else {
            $messageFlash = __d('croogo', 'Term could not be deleted. Please, try again.');
            $flashMethod = 'error';
        }

        $this->Flash->{$flashMethod}($messageFlash);

        return $this->redirect($redirectUrl);
    }

    /**
     * Admin moveup
     *
     * @param int $id
     * @param int $vocabularyId
     * @param int $step
     * @return void
     * @access public
     */
    public function moveup($id = null, $vocabularyId = null, $step = 1)
    {
        $this->__move('up', $id, $vocabularyId, $step);
    }

    /**
     * Admin movedown
     *
     * @param int $id
     * @param int $vocabularyId
     * @param int $step
     * @return void
     * @access public
     */
    public function movedown($id = null, $vocabularyId = null, $step = 1)
    {
        $this->__move('down', $id, $vocabularyId, $step);
    }

    /**
     * __move
     *
     * @param string $direction either 'up' or 'down'
     * @param int $id
     * @param int $vocabularyId
     * @param int $step
     * @access private
     */
    private function __move($direction, $id, $vocabularyId, $step)
    {
        $redirectUrl = ['action' => 'index', 'vocabulary_id' => $vocabularyId];
        $response = $this->_ensureVocabularyIdExists($vocabularyId, $redirectUrl);
        if ($response instanceof Response) {
            return $response;
        }
        $response = $this->_ensureTermExists($id, $redirectUrl);
        if ($response instanceof Response) {
            return $response;
        }
        $taxonomyId = $this->Terms->Taxonomies->termInVocabulary($id, $vocabularyId);
        $response = $this->_ensureVocabularyIdExists($vocabularyId, $redirectUrl);
        if ($response instanceof Response) {
            return $response;
        }

        $this->Terms->setScopeForTaxonomy($vocabularyId);

        $taxonomy = $this->Terms->Taxonomies->get($taxonomyId);
        if ($this->Terms->Taxonomies->{'move' . ucfirst($direction)}($taxonomy, $step)) {
            $messageFlash = __d('croogo', 'Moved %s successfully', $direction);
            $cssClass = ['class' => 'success'];
        } else {
            $messageFlash = __d('croogo', 'Could not move %s', $direction);
            $cssClass = ['class' => 'error'];
        }
        $this->Flash->{$cssClass['class']}($messageFlash);

        return $this->redirect($redirectUrl);
    }

    /**
     * Get default type from Vocabulary
     */
    private function __getDefaultType($vocabulary)
    {
        $defaultType = null;
        if (isset($vocabulary->types[0])) {
            $defaultType = $vocabulary->types[0];
        }
        $typeId = $this->request->query('type_id');
        if ($typeId) {
            $defaultType = collection($vocabulary['types'])->match([
                'id' => $typeId,
            ]);
        }

        return $defaultType;
    }

    /**
     * Check that Term exists or flash and redirect to $url when it is not found
     *
     * @param int $idTerm Id
     * @param string $url Redirect Url
     * @return bool True if Term exists
     */
    public function _ensureTermExists($id, $url = null)
    {
        $redirectUrl = is_null($url) ? $this->_redirectUrl : $url;
        try {
            $this->Terms->get($id);
        } catch (RecordNotFoundException $exception) {
            $this->Flash->error(__d('croogo', 'Invalid Term ID.'));

            return $this->redirect($redirectUrl);
        }
    }

    /**
     * Checks that Taxonomy exists or flash redirect to $url when it is not found
     *
     * @param int $idTerm Id
     * @param int $vocabularyIdVocabulary Id
     * @param string $url Redirect Url
     * @return bool True if Term exists
     */
    public function _ensureTaxonomyExists($id, $vocabularyId, $url = null)
    {
        $redirectUrl = is_null($url) ? $this->_redirectUrl : $url;
        $count = $this->Terms->Taxonomies->find()
            ->where(['term_id' => $id, 'vocabulary_id' => $vocabularyId])
            ->count();
        if (!$count) {
            $this->Flash->error(__d('croogo', 'Invalid Taxonomy.'));

            return $this->redirect($redirectUrl);
        }
    }

    /**
     * Checks that Vocabulary exists or flash redirect to $url when it is not found
     *
     * @param int $vocabularyIdVocabulary Id
     * @param string $url Redirect Url
     * @return bool True if Term exists
     */
    public function _ensureVocabularyIdExists($vocabularyId, $url = null)
    {
        $redirectUrl = is_null($url) ? $this->_redirectUrl : $url;
        if (!$vocabularyId) {
            return $this->redirect($redirectUrl);
        }

        try {
            $this->Terms->Vocabularies->get($vocabularyId);
        } catch (RecordNotFoundException $recordNotFoundException) {
            $this->Flash->error(__d('croogo', 'Invalid Vocabulary ID.'));

            return $this->redirect($redirectUrl);
        }
    }
}
