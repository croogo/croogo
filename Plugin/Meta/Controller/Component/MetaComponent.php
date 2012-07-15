<?php

class MetaComponent extends Component {

	public function startup(Controller $controller) {
		if (isset($controller->request->params['admin']) &&
		    !empty($controller->request->data['Meta']))
		{
			$unlockedFields = array();
			foreach ($controller->request->data['Meta'] as $uuid => $fields) {
				foreach ($fields as $field => $vals) {
					$unlockedFields[] = 'Meta.' . $uuid . '.' . $field;
				}
			}
			$controller->Security->unlockedFields += $unlockedFields;
		}
	}

}

