<?php

App::uses('ModelBehavior', 'Model/Behavior');

/**
 * TaxonomizableBehavior
 *
 * @category Taxonomy.Model.Behavior
 * @package  Croogo.Taxonomy.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TaxonomizableBehavior extends ModelBehavior {

/**
 * Setup behavior
 *
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->settings[$model->alias] = $config;
	}

/**
 * beforeSave
 *
 * @return bool
 */
	public function beforeSave(Model $model, $options = array()) {
		$model->cacheTerms();
		return true;
	}

/**
 * Caches Term in `terms` field
 *
 * @return void
 */
	public function cacheTerms(Model $model) {
		if (isset($model->data['Taxonomy']['Taxonomy'])) {
			$taxonomyIds = $model->data['Taxonomy']['Taxonomy'];
			$taxonomies = $model->Taxonomy->find('all', array(
				'conditions' => array(
					'Taxonomy.id' => $taxonomyIds,
				),
			));
			$terms = Hash::combine($taxonomies, '{n}.Term.id', '{n}.Term.slug');
			$model->data[$model->alias]['terms'] = $model->encodeData($terms, array(
				'trim' => false,
				'json' => true,
			));
		}
	}

}
