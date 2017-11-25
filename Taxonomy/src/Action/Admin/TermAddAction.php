<?php

namespace Croogo\Taxonomy\Action\Admin;

use Crud\Action\BaseAction;

class TermAddAction extends BaseAction
{
    protected $_defaultConfig = [
        'enabled' => true,
    ];

    /**
     * Implements Term add
     *
     * @param int $vocabularyId
     * @access public
     */
    protected function _handle()
    {
        $controller = $this->_controller();
        $request = $controller->request;
        $vocabularyId = $request->query('vocabulary_id');

        $response = $controller->_ensureVocabularyIdExists($vocabularyId);
        if ($response instanceof Response) {
            return $response;
        }

        $vocabulary = $controller->Terms->Vocabularies->get($vocabularyId);

        $term = $controller->Terms->newEntity();
        $controller->set('term', $term);

        if ($request->is('post')) {
            $term = $controller->Terms->patchEntity($term, $request->data);

            $taxonomy = $controller->Terms->add($term, $vocabularyId);
            if ($taxonomy) {
                $controller->Flash->success(__d('croogo', 'Term saved successfuly.'));

                $redirectUrl = [
                    'action' => 'edit',
                    $taxonomy->term_id,
                    'vocabulary_id' => $vocabularyId,
                ];
                if (!$term->has('_apply')) {
                    $redirectUrl = [
                        'action' => 'index',
                        'vocabulary_id' => $vocabularyId,
                    ];
                }
                return $controller->redirect($redirectUrl);
            } else {
                $controller->Flash->error(__d('croogo', 'Term could not be added to the vocabulary. Please try again.'));
            }
        }
        $parentTree = $controller->Terms->Taxonomies->getTree($vocabulary->alias, ['taxonomyId' => true]);
        $controller->set(compact('vocabulary', 'parentTree', 'vocabularyId'));
    }

}
