<?php

App::uses('Node', 'Nodes.Model');

/**
 * Attachment Model
 *
 * PHP version 5
 *
 * @category FileManager.Model
 * @package  Croogo.FileManager.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Attachment extends Node {

/**
 * alias
 */
	public $alias = 'Attachment';

/**
 * useTable
 */
	public $useTable = 'nodes';

/**
 * type
 */
	public $type = 'attachment';

/**
 * Uploads directory
 *
 * relative to the webroot.
 *
 * @var string
 * @access public
 */
	public $uploadsDir = 'uploads';

/**
 * Save uploaded file
 *
 * @param array $data data as POSTed from form
 * @return array|boolean false for errors or array containing fields to save
 */
	protected function _saveUploadedFile($data) {
		$file = $data[$this->alias]['file'];
		unset($data[$this->alias]['file']);

		// check if file with same path exists
		$destination = WWW_ROOT . $this->uploadsDir . DS . $file['name'];
		if (file_exists($destination)) {
			$newFileName = String::uuid() . '-' . $file['name'];
			$destination = WWW_ROOT . $this->uploadsDir . DS . $newFileName;
		} else {
			$newFileName = $file['name'];
		}

		// remove the extension for title
		if (explode('.', $file['name']) > 0) {
			$fileTitleE = explode('.', $file['name']);
			array_pop($fileTitleE);
			$fileTitle = implode('.', $fileTitleE);
		} else {
			$fileTitle = $file['name'];
		}

		$data[$this->alias]['title'] = $fileTitle;
		$data[$this->alias]['slug'] = $newFileName;
		$data[$this->alias]['mime_type'] = $file['type'];
		$data[$this->alias]['type'] = $this->type;
		$data[$this->alias]['path'] = '/' . $this->uploadsDir . '/' . $newFileName;
		// move the file
		$moved = move_uploaded_file($file['tmp_name'], $destination);
		if ($moved) {
			return $data;
		}

		return false;
	}

/**
 * Saves model data
 *
 * @see Model::save()
 */
	public function save($data = null, $validate = true, $fieldList = array()) {
		if (isset($data[$this->alias]['file']['tmp_name'])) {
			$data = $this->_saveUploadedFile($data);
		}
		if (!$data) {
			return $this->invalidate('file', __d('croogo', 'Error during file upload'));
		}
		return parent::save($data, $validate, $fieldList);
	}

/**
 * Removes record for given ID.
 *
 * @see Model::delete()
 */
	public function delete($id = null, $cascade = true) {
		$attachment = $this->find('first', array(
			'conditions' => array(
				$this->alias . '.id' => $id,
				$this->alias . '.type' => $this->type,
			),
		));

		$filename = $attachment[$this->alias]['slug'];
		$fullpath = WWW_ROOT . $this->uploadsDir . DS . $filename;
		if (file_exists($fullpath)) {
			$result = unlink(WWW_ROOT . $this->uploadsDir . DS . $filename);
			if ($result) {
				return parent::delete($id, $cascade);
			} else {
				return false;
			}
		} else {
			return parent::delete($id, $cascade);
		}
	}

}
