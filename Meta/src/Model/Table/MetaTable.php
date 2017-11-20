<?php

namespace Croogo\Meta\Model\Table;

use Croogo\Core\Model\Table\CroogoTable;

/**
 * Meta
 *
 * @category Meta.Model
 * @package  Croogo.Meta
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MetaTable extends CroogoTable
{
    protected $_quoted;

    protected $_displayFields = [
        'key',
        'value',
    ];

    protected $_editFields = [
        'key',
        'value',
    ];

    public function initialize(array $config)
    {
        $this->table('meta');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Croogo/Core.Trackable');
        $this->addBehavior('Search.Search');

        parent::initialize($config);
    }

    /**
     * @return void
     */
    public function beforeSave()
    {
        $this->_quoted = $this->connection()
            ->driver()
            ->autoQuoting();
        $this->connection()
            ->driver()
            ->autoQuoting(true);
    }

    /**
     * @return void
     */
    public function afterSave()
    {
        $this->connection()
            ->driver()
            ->autoQuoting($this->_quoted);
    }
}
