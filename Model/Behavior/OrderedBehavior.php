<?php
/**
 * OrderedBehavior
 *
 * @developer Alexander Morland ( aka. alkemann)
 * @license MIT
 * @version 2.1
 * @modified 27. august 2008
 *
 * This behavior lets you order items in a very similar way to the tree
 * behavior, only there is only 1 level. You can however have many
 * independent lists in one table. Usually you use a foreign key to
 * set / see what list you are in (see example bellow) or if you have
 * just one list for the entire table, you can do that too.
 *
 * What it does:
 *
 * It manages the creation and updating of the order field. It
 * also sets the models order property to this field. When adding new
 * nodes or deleting old ones, this behavior will do the necisary changes
 * to keep the list working properly. It is build to be completely
 * automagic after the initial configuration by letting it know
 * your foreign_key and weight fields.
 *
 * Usage example :
 *
 * Lets say you have books with pages and want the pages ordered
 * by page number (obviously a book sorted alphabetically would be
 * silly). So you have these models:
 *
 * Book hasMany Page
 * Page belongsTo Book
 *
 * The Page model has fields :
 *
 * id
 * content
 * book_id
 * page_number
 *
 * To set up this behavior we add this property to the Page model :
 *
 * public $actsAs = array('Ordered' => array(
 * 			'field' 		=> 'page_number',
 * 			'foreign_key' 	=> 'book_id'
 * 		));
 *
 * Now when you save a new page (no changes needed to action or view,
 * but leave page_number out of the form), it will be added to the end
 * of the book.
 *
 * When deleting, the weights will automatically be adjusted to fill in
 * the vacum.
 *
 * NB! Note that if using Model::deleteAll() it is VERY important that you
 * assign it to use callbacks 'beforeDelete' and 'afterDelete', like this:
 *
 * // in controller action
 * $this->Page->deleteAll(array('user_id'=>22),true,array('beforeDelete','afterDelete'));
 *
 * Now lets say the last two pages to be created got made in the wrong
 * order, so you want to move the last page "up" one space. With the
 * a simple controller call to the model like this that can be achieved:
 *
 * // in a controller action :
 * $this->Page->moveUp($id);
 * // the id here is the id of the newest page
 *
 * You find that the first page you made is suppose to be the 5 pages later:
 *
 * // in a controller action :
 * $this->Page->moveDown($id, 5);
 *
 * Also you discovered that in the first page got put in the middle. This
 * can easily be moved first by doing this :
 *
 * // in a controller action :
 * $this->Page->moveUp($id,true);
 * // true will move it to the extre in that direction
 *
 * You can also use actions to find out if the node is first or last page :
 *
 *  - isFirst($id)
 *  - isLast($id)
 *
 * And a last feature is the ability to sort the list by any field
 * you want and have it set weights based on that. You do that like this :
 *
 * //in controller action :
 * $this->Page->sortby('content DESC', $book_id);
 * // dont ask me why you would sort the pages of a book by its content lol
 *
 * Note that this behaviour will also let you sort an entire table as one list.
 * To do that you simply set the 'foreign_key' to false (and dont create the field
 * in the table). Now there will only be one set of weights. (Note you need the weight
 * field as normal)
 *
 * @author Alexander Morland aka alkemann
 * @license MIT
 * @modified 17. nov. 2008 (model independent settings)
 * @version 2.1.3
 *
 */
class OrderedBehavior extends ModelBehavior {

	public $name = 'Ordered';

/**
 * field : (string) The field to be ordered by.
 *
 * foreign_key : (string) The field to identify one SET by.
 *               Each set has their own order (ie they start at 1).
 *               Set to FALSE to not use this feature (and use only 1 set)
 */
	protected $_defaults = array('field' => 'weight', 'foreign_key' => 'order_id');

	public function setup(Model $Model, $config = array()) {
		if (!is_array($config)) {
			$config = array();
		}
		$this->settings[$Model->alias] = array_merge($this->_defaults, $config);
		$Model->order = $Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' ASC';
	}

	public function beforeDelete(Model $Model, $cascade = true) {
		$Model->read();
		$highest = $this->_highest($Model);
		if (!empty($Model->data) && ($Model->data[$Model->alias][$Model->primaryKey] == $highest[$Model->alias][$Model->primaryKey])) {
			$Model->data = null;
		}
		return true;
	}

	public function afterDelete(Model $Model) {
		if ($Model->data) {
			// What was the weight of the deleted model?
			$oldWeight = $Model->data[$Model->alias][$this->settings[$Model->alias]['field']];

			// update the weight of all models of higher weight by
			$action = array($this->settings[$Model->alias]['field'] => $this->settings[$Model->alias]['field'] . ' - 1');
			$conditions = array(
					$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' >' => $oldWeight);
			if ($this->settings[$Model->alias]['foreign_key']) {
				$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['foreign_key']] = $Model->data[$Model->alias][$this->settings[$Model->alias]['foreign_key']];
			}

			// decreasing them by 1
			return $Model->updateAll($action, $conditions);
		}
		return true;
	}

/**
 * Sets the weight for new items so they end up at end
 *
 * @todo add new model with weight. clean up after
 * @param Model $Model
 */
	public function beforeSave(Model $Model) {
		// Check if weight id is set. If not add to end, if set update all
		// rows from ID and up
		if (!isset($Model->data[$Model->alias][$Model->primaryKey])) {
			// get highest current row
			$highest = $this->_highest($Model);
			// set new weight to model as last by using current highest one + 1
			$Model->data[$Model->alias][$this->settings[$Model->alias]['field']] = $highest[$Model->alias][$this->settings[$Model->alias]['field']] + 1;
		}
		return true;
	}

/**
 * Moving a node to specific weight, it will shift the rest of the table to make room.
 *
 * @param Object $Model
 * @param int $id The id of the node to move
 * @param int $newWeight the new weight of the node
 * @return boolean True of move successful
 */
	public function moveTo(Model $Model, $id = null, $newWeight = null) {
		if (!$id || !$newWeight || $newWeight < 1) {
			return false;
		}
		$highest = $this->_highest($Model);
		// fetch the model and its old weight
		$oldWeight = $this->_read($Model, $id);

		//check if new weight is too big
		if ($newWeight > $highest[$Model->alias][$this->settings[$Model->alias]['field']]) {
			return false;
		}
		if ($newWeight === true && $oldWeight == 0) {
			$newWeight = $highest[$Model->alias][$this->settings[$Model->alias]['field']] + 1;
		}
		if (empty($Model->data)) {
			return false;
		}
		$conditions = array();
		if ($this->settings[$Model->alias]['foreign_key']) {
			$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['foreign_key']] = $Model->data[$Model->alias][$this->settings[$Model->alias]['foreign_key']];
		}

		// give Model new weight
		$Model->data[$Model->alias][$this->settings[$Model->alias]['field']] = $newWeight;
		if ($newWeight == $oldWeight) {
			// move to same location?
			return false;
		} elseif ($oldWeight == 0) {
			$action = array(
					$Model->alias . '.' . $this->settings[$Model->alias]['field'] => $Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' + 1');
			$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' >='] = $newWeight;
		} elseif ($newWeight > $oldWeight) {
			// move all nodes that have weight > old_weight AND <= new_weight up one (-1)
			$action = array(
					$Model->alias . '.' . $this->settings[$Model->alias]['field'] => $Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' - 1');
			$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' <='] = $newWeight;
			$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' >'] = $oldWeight;
		} else { // $newWeight < $oldWeight
			// move all where weight >= new_weight AND < old_weight down one (+1)
			$action = array(
					$Model->alias . '.' . $this->settings[$Model->alias]['field'] => $Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' + 1');
			$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' >='] = $newWeight;
			$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' <'] = $oldWeight;

		}
		$Model->updateAll($action, $conditions);
		return $Model->save(null, false);
	}

/**
 * Take in an order array and sorts the list based on that order specification
 * and creates new weights for it. If no foreign key is supplied, all lists
 * will be sorted.
 *
 * @todo foreign key independent
 * @param Object $Model
 * @param array $order
 * @param mixed $foreignKey
 * $returns boolean true if successfull
 */
	public function sortBy(Model $Model, $order, $foreignKey = null) {
		$fields = array($Model->primaryKey, $this->settings[$Model->alias]['field']);
		$conditions = array(1 => 1);
		if ($this->settings[$Model->alias]['foreign_key']) {
			if (!$foreignKey) {
				return false;
			}
			$fields[] = $this->settings[$Model->alias]['foreign_key'];
			$conditions = array(
					$Model->alias . '.' . $this->settings[$Model->alias]['foreign_key'] => $foreignKey);
		}

		$all = $Model->find('all', array(
				'fields' => $fields,
				'conditions' => $conditions,
				'recursive' => -1,
				'order' => $order));
		$i = 1;
		foreach ($all as $key => $one) {
			$all[$key][$Model->alias][$this->settings[$Model->alias]['field']] = $i++;
		}
		return $Model->saveAll($all);
	}

/**
 * Reorder the node, by moving it $number spaces up. Defaults to 1
 *
 * If the node is the first node (or less then $number spaces from first)
 * this method will return false.
 *
 * @param AppModel $Model
 * @param mixed $id The ID of the record to move
 * @param mixed $number how many places to move the node or true to move to last position
 * @return boolean true on success, false on failure
 * @access public
 */
	public function moveUp(Model $Model, $id = null, $number = 1) {
		if (!$id) {
			if ($Model->id) {
				$id = $Model->id;
			} elseif (!empty($Model->data) && isset($Model->data[$Model->alias][$Model->primaryKey])) {
				$id = $Model->data[$Model->alias][$Model->primaryKey];
			} else {
				return false;
			}
		}
		$oldWeight = $this->_read($Model, $id);
		if (empty($Model->data)) {
			return false;
		}
		if (is_numeric($number)) {
			if ($number == 1) { // move 1 space
				$previous = $this->_previous($Model);
				if (!$previous) {
					return false;
				}
				$Model->data[$Model->alias][$this->settings[$Model->alias]['field']] = $previous[$Model->alias][$this->settings[$Model->alias]['field']];

				$previous[$Model->alias][$this->settings[$Model->alias]['field']] = $oldWeight;

				$data[0] = $Model->data;
				$data[1] = $previous;

				return $Model->saveAll($data, array('validate' => false));

			} elseif ($number < 1) { // cant move 0 or negative spaces
				return false;
			} else { // move Model up N spaces UP
				if ($this->settings[$Model->alias]['foreign_key']) {
					$conditions = array(
							$Model->alias . '.' . $this->settings[$Model->alias]['foreign_key'] => $Model->data[$Model->alias][$this->settings[$Model->alias]['foreign_key']]);
				} else {
					$conditions = array();
				}

				// find the one occupying new space and its weight
				$newWeight = $Model->data[$Model->alias][$this->settings[$Model->alias]['field']] - $number;
				// check if new weight is possible. else move last
				if (!$this->_findByWeight($Model, $newWeight)) {
					return false;
				}
				$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' >='] = $newWeight;
				$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' <'] = $oldWeight;
				// increase weight of all where weight > new weight and id != Model.id
				$Model->updateAll(array(
						$this->settings[$Model->alias]['field'] => $Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' + 1'), $conditions);

				// set Model weight to new weight and save it
				$Model->data[$Model->alias][$this->settings[$Model->alias]['field']] = $newWeight;
				return $Model->save(null, false);
			}
		} elseif (is_bool($number) && $number && $Model->data[$Model->alias][$this->settings[$Model->alias]['field']] != 1) { // move Model FIRST;
			if ($this->settings[$Model->alias]['foreign_key']) {
				$conditions = array(
						$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' <' => $oldWeight,
						$Model->alias . '.' . $this->settings[$Model->alias]['foreign_key'] => $Model->data[$Model->alias][$this->settings[$Model->alias]['foreign_key']]);
			} else {
				$conditions = array(
						$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' <' => $oldWeight);
			}
			$Model->id = $Model->data[$Model->alias][$Model->primaryKey];
			$Model->saveField($this->settings[$Model->alias]['field'], 0);
			$Model->updateAll(array( // update
					$Model->alias . '.' . $this->settings[$Model->alias]['field'] => $Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' + 1'), $conditions);

			return true;
		} else { // $number is neither a number nor a bool
			return false;
		}
	}

/**
 * This will create weights based on display field. The purpose of the method is to create
 * weights for tables that existed before this behavior was added.
 *
 * @param Object $Model
 * @return boolean success
 */
	public function resetWeights(Model $Model) {
		if ($this->settings[$Model->alias]['foreign_key']) {
			$temp = $Model->find('all', array(
					'fields' => $this->settings[$Model->alias]['foreign_key'],
					'group' => $this->settings[$Model->alias]['foreign_key'],
					'recursive' => -1));
			$foreignKeys = Set::extract($temp, '{n}.' . $Model->alias . '.' . $this->settings[$Model->alias]['foreign_key']);
			foreach ($foreignKeys as $fk) {
				$all = $Model->find('all', array(
						'conditions' => array($this->settings[$Model->alias]['foreign_key'] => $fk),
						'fields' => array(
								$Model->displayField,
								$Model->primaryKey,
								$this->settings[$Model->alias]['field'],
								$this->settings[$Model->alias]['foreign_key']),
						'order' => $Model->displayField));
				$i = 1;
				foreach ($all as $key => $one) {
					$all[$key][$Model->alias][$this->settings[$Model->alias]['field']] = $i++;
				}
				if (!$Model->saveAll($all)) {
					return false;
				}
			}
		} else {
			$all = $Model->find('all', array(
					'fields' => array(
							$Model->displayField,
							$Model->primaryKey,
							$this->settings[$Model->alias]['field']),
					'order' => $Model->displayField));
			$i = 1;
			foreach ($all as $key => $one) {
				$all[$key][$Model->alias][$this->settings[$Model->alias]['field']] = $i++;
			}
			if (!$Model->saveAll($all)) {
				return false;
			}
		}
		return true;
	}

/**
 * Reorder the node, by moving it $number spaces down. Defaults to 1
 *
 * If the node is the last node (or less then $number spaces from last)
 * this method will return false.
 *
 * @param AppModel $Model
 * @param mixed $id The ID of the record to move
 * @param mixed $number how many places to move the node or true to move to last position
 * @return boolean true on success, false on failure
 * @access public
 */
	public function moveDown(Model $Model, $id = null, $number = 1) {
		if (!$id) {
			if ($Model->id) {
				$id = $Model->id;
			} elseif (!empty($Model->data) && isset($Model->data[$Model->alias][$Model->primaryKey])) {
				$id = $Model->data[$Model->alias][$Model->primaryKey];
			} else {
				return false;
			}
		}
		$oldWeight = $this->_read($Model, $id);
		if (empty($Model->data)) {
			return false;
		}
		if (is_numeric($number)) {
			if ($number == 1) { // move node 1 space down
				$next = $this->_next($Model);
				if (!$next) { // it is the last node
					return false;
				}
				// switch the node's weight around
				$Model->data[$Model->alias][$this->settings[$Model->alias]['field']] = $next[$Model->alias][$this->settings[$Model->alias]['field']];

				$next[$Model->alias][$this->settings[$Model->alias]['field']] = $oldWeight;

				// create an array of the two nodes and save them
				$data[0] = $Model->data;
				$data[1] = $next;
				return $Model->saveAll($data, array('validate' => false));

			} elseif ($number < 1) { // cant move 0 or negative number of spaces
				return false;
			} else { // move Model up N spaces DWN
				if ($this->settings[$Model->alias]['foreign_key']) {
					$conditions = array(
							$Model->alias . '.' . $this->settings[$Model->alias]['foreign_key'] => $Model->data[$Model->alias][$this->settings[$Model->alias]['foreign_key']]);
				} else {
					$conditions = array();
				}

				// find the one occupying new space and its weight
				$newWeight = $Model->data[$Model->alias][$this->settings[$Model->alias]['field']] + $number;
				// check if new weight is possible. else move last
				if (!$this->_findByWeight($Model, $newWeight)) {
					return false;
				}

				// increase weight of all where weight > new weight and id != Model.id
				$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' <='] = $newWeight;
				$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' >'] = $oldWeight;

				$Model->updateAll(array(
						$this->settings[$Model->alias]['field'] => $this->settings[$Model->alias]['field'] . ' - 1'), $conditions);

				// set Model weight to new weight and save it
				$Model->data[$Model->alias][$this->settings[$Model->alias]['field']] = $newWeight;
				return $Model->save(null, false);
			}

		} elseif (is_bool($number) && $number) { // move Model LAST;
			if ($this->settings[$Model->alias]['foreign_key']) {
				$conditions = array(
						$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' >' => $oldWeight,
						$Model->alias . '.' . $this->settings[$Model->alias]['foreign_key'] => $Model->data[$Model->alias][$this->settings[$Model->alias]['foreign_key']]);
			} else {
				$conditions = array(
						$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' >' => $oldWeight);
			}

			// get highest weighted row
			$highest = $this->_highest($Model);
			// check of Model is allready highest
			if ($highest[$Model->alias][$Model->primaryKey] == $Model->data[$Model->alias][$Model->primaryKey]) {
				return false;
			}
			// Save models as highest +1
			$Model->saveField($this->settings[$Model->alias]['field'], $highest[$Model->alias][$this->settings[$Model->alias]['field']] + 1);
			// updated all by taking off 1
			$Model->updateAll(array( // action
					$Model->alias . '.' . $this->settings[$Model->alias]['field'] => $Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' - 1'), $conditions);

			return true;
		} else { // $number is neither a number nor a bool
			return false;
		}
	}

/**
 * Returns true if the specified item is the first item
 *
 * @param Model $Model
 * @param Int $id
 * @return Boolean, true if it is the first item, false if not
 */
	public function isFirst(Model $Model, $id = null) {
		if (!$id) {
			if ($Model->id) {
				$id = $Model->id;
			} elseif (!empty($Model->data) && isset($Model->data[$Model->alias][$Model->primaryKey])) {
				$id = $Model->id = $Model->data[$Model->alias][$Model->primaryKey];
			} else {
				return false;
			}
		} else {
			$Model->id = $id;
		}
		$Model->read();

		$first = $this->_read($Model, $id);
		if ($Model->data[$Model->alias][$this->settings[$Model->alias]['field']] == 1) {
			return true;
		} else {
			return false;
		}
	}

/**
 * Returns true if the specified item is the last item
 *
 * @param Model $Model
 * @param Int $id
 * @return Boolean, true if it is the last item, false if not
 */
	public function isLast(Model $Model, $id = null) {
		if (!$id) {
			if ($Model->id) {
				$id = $Model->id;
			} elseif (!empty($Model->data) && isset($Model->data[$Model->alias][$Model->primaryKey])) {
				$id = $Model->id = $Model->data[$Model->alias][$Model->primaryKey];
			} else {
				return false;
			}
		} else {
			$Model->id = $id;
		}
		$Model->read();
		$last = $this->_highest($Model);
		return ($last[$Model->alias][$Model->primaryKey] == $id);
	}

/**
 * Removing an item from the list means to set its field to 0 and updating the other items to be "complete"
 *
 * @param Model $Model
 * @param int $id
 * @return boolean
 */
	public function removefromlist(Model $Model, $id) {
		$this->_read($Model, $id);
		$oldWeight = $Model->data[$Model->alias][$this->settings[$Model->alias]['field']];
		$action = array(
				$Model->alias . '.' . $this->settings[$Model->alias]['field'] => $Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' - 1');
		$conditions = array(
				$Model->alias . '.' . $this->settings[$Model->alias]['field'] . ' >' => $oldWeight);
		if ($this->settings[$Model->alias]['foreign_key']) {
			$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['foreign_key']] = $Model->data[$Model->alias][$this->settings[$Model->alias]['foreign_key']];
		}
		$data = $Model->data;
		$data[$Model->alias][$this->settings[$Model->alias]['field']] = 0;
		if (!$Model->save($data, false)) {
			return false;
		}
		return $Model->updateAll($action, $conditions);
	}

	protected function _findbyweight(Model $Model, $weight) {
		$conditions = array($this->settings[$Model->alias]['field'] => $weight);
		$fields = array($Model->primaryKey, $this->settings[$Model->alias]['field']);
		if ($this->settings[$Model->alias]['foreign_key']) {
			$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['foreign_key']] = $Model->data[$Model->alias][$this->settings[$Model->alias]['foreign_key']];
			$fields[] = $this->settings[$Model->alias]['foreign_key'];
		}
		return $Model->find('first', array(
				'conditions' => $conditions,
				'order' => $this->settings[$Model->alias]['field'] . ' DESC',
				'fields' => $fields,
				'recursive' => -1));
	}

	protected function _highest(Model $Model) {
		$options = array(
				'order' => $this->settings[$Model->alias]['field'] . ' DESC',
				'fields' => array($Model->primaryKey, $this->settings[$Model->alias]['field']),
				'recursive' => -1);
		if ($this->settings[$Model->alias]['foreign_key']) {
			if (empty($Model->data) || !isset($Model->data[$Model->alias][$this->settings[$Model->alias]['foreign_key']])) {
				$this->_read($Model, $Model->id);
			}
			$options['conditions'] = array(
					$Model->alias . '.' . $this->settings[$Model->alias]['foreign_key'] => $Model->data[$Model->alias][$this->settings[$Model->alias]['foreign_key']]);
			$options['fields'][] = $this->settings[$Model->alias]['foreign_key'];
		}
		$tempModelId = $Model->id;
		$Model->id = null;
		$last = $Model->find('first', $options);
		$Model->id = $tempModelId;
		return $last;
	}

	protected function _previous(Model $Model) {
		$conditions = array(
				$this->settings[$Model->alias]['field'] => $Model->data[$Model->alias][$this->settings[$Model->alias]['field']] - 1);
		$fields = array($Model->primaryKey, $this->settings[$Model->alias]['field']);
		if ($this->settings[$Model->alias]['foreign_key']) {
			$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['foreign_key']] = $Model->data[$Model->alias][$this->settings[$Model->alias]['foreign_key']];
			$fields[] = $this->settings[$Model->alias]['foreign_key'];
		}
		return $Model->find('first', array(
				'conditions' => $conditions,
				'order' => $this->settings[$Model->alias]['field'] . ' DESC',
				'fields' => $fields,
				'recursive' => -1));
	}

	protected function _next(Model $Model) {
		$conditions = array(
				$this->settings[$Model->alias]['field'] => $Model->data[$Model->alias][$this->settings[$Model->alias]['field']] + 1);
		$fields = array($Model->primaryKey, $this->settings[$Model->alias]['field']);
		if ($this->settings[$Model->alias]['foreign_key']) {
			$conditions[$Model->alias . '.' . $this->settings[$Model->alias]['foreign_key']] = $Model->data[$Model->alias][$this->settings[$Model->alias]['foreign_key']];
			$fields[] = $this->settings[$Model->alias]['foreign_key'];
		}
		return $Model->find('first', array(
				'conditions' => $conditions,
				'order' => $this->settings[$Model->alias]['field'] . ' DESC',
				'fields' => $fields,
				'recursive' => -1));
	}

	protected function _all(Model $Model) {
		$options = array(
				'order' => $this->settings[$Model->alias]['field'] . ' DESC',
				'fields' => array($Model->primaryKey, $this->settings[$Model->alias]['field']),
				'recursive' => -1);
		if ($this->settings[$Model->alias]['foreign_key']) {
			$options['conditions'] = array(
					$this->settings[$Model->alias]['foreign_key'] => $Model->data[$Model->alias][$this->settings[$Model->alias]['foreign_key']]);
			$options['fields'][] = $this->settings[$Model->alias]['foreign_key'];
		}
		return $Model->find('all', $options);
	}

	protected function _read(Model $Model, $id) {
		$Model->id = $id;
		$fields = array($Model->primaryKey, $this->settings[$Model->alias]['field']);
		if ($this->settings[$Model->alias]['foreign_key']) {
			$fields[] = $this->settings[$Model->alias]['foreign_key'];
		}
		$Model->data = $Model->find('first', array(
				'fields' => $fields,
				'conditions' => array($Model->primaryKey => $id),
				'recursive' => -1));
		return $Model->data[$Model->alias][$this->settings[$Model->alias]['field']];
	}
}
