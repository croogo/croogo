<?php

namespace Croogo\Core\Model\Behavior;

use Cake\Database\Query;
use Cake\ORM\Behavior;

class VisibilityBehavior extends Behavior
{

	public function findVisibilityRole(Query $query, array $options = [])
	{
		$query->where([
			'AND' => [
				[
					'OR' => [
						'Links.visibility_roles IS NULL',
						'Links.visibility_roles LIKE' => '%"' . $options['role_id'] . '"%',
					],
				],
			],
		]);

		return $query;
	}
}
