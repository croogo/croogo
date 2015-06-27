<?php

namespace Croogo\Dashboards\View\Cell;

use Cake\Event\EventManager;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\View\Cell;

class BlogFeedCell extends Cell
{

	/**
	 * @var string Alias of dashboard item
	 */
	private $alias;

	public function __construct(
		Request $request = null,
		Response $response = null,
		EventManager $eventManager = null,
		array $cellOptions = []
	)
	{
		parent::__construct($request, $response, $eventManager, $cellOptions);

		$this->alias = $cellOptions['alias'];
	}


	public function dashboard()
	{
		$this->set('alias', $this->alias);
	}

}
