<?php

namespace Croogo\Dashboards\Model\Table;

use Croogo\Core\Model\Table\CroogoTable;

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
class DashboardsTable extends CroogoTable
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('dashboards');
		$this->addBehavior('Timestamp');
		$this->addBehavior('ADmad/Sequence.Sequence', [
			'order' => 'weight',
			'scope' => ['user_id', 'column'],
		]);
        $this->belongsTo('Users', [
            'className' => 'Croogo/Users.Users'
        ]);
        $this->connection()->getDriver()->enableAutoQuoting();
    }
}
