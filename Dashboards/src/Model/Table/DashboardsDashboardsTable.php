<?php

namespace Croogo\Dashboards\Model\Table;

use Cake\ORM\Table;

/**
 * Dashboard Model
 *
 * @category Dashboards.Model
 * @package  Croogo.Dashboards.Model
 * @version  2.2
 * @author   Walther Lalk <emailme@waltherlalk.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class DashboardsDashboardsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('dashboards');
//		$this->addBehavior('Croogo/Core.Ordered', [
//			'field' => 'weight',
//			'foreign_key' => 'user_id',
//		]);
        $this->belongsTo('Users', [
            'className' => 'Croogo/Users.Users'
        ]);
    }
}
