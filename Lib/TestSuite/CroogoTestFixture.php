<?php

class CroogoTestFixture extends CakeTestFixture {

	protected function _fixSequence($db) {
		$sql = sprintf("
			SELECT setval(pg_get_serial_sequence('%s', 'id'), (SELECT MAX(id) FROM %s))",
			$this->table, $this->table);

		$db->execute($sql);
	}

	public function insert($db) {
		$result = parent::insert($db);
		if ($result === true && $db instanceof Postgres) {
			$this->_fixSequence($db);
		}
		return $result;
	}

}
