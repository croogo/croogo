<?php

App::uses('CakeTestFixture', 'TestSuite/Fixture');

/**
 * CroogoTestFixture class
 *
 * PHP version 5
 *
 * @category TestSuite
 * @package  Croogo
 * @version  1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoTestFixture extends CakeTestFixture {

/**
 * _fixSequence
 *
 * @param Postgres $db
 */
	protected function _fixSequence($db) {
		$sql = sprintf("
			SELECT setval(pg_get_serial_sequence('%s', 'id'), (SELECT MAX(id) FROM %s))",
			$this->table, $this->table);

		$db->execute($sql);
	}

/**
 * insert
 *
 * @param Object $db
 * @return array
 */
	public function insert($db) {
		$result = parent::insert($db);
		if ($result === true && $db instanceof Postgres) {
			$this->_fixSequence($db);
		}
		return $result;
	}

}
