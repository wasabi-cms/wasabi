<?php
/**
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the below copyright notice.
 *
 * @copyright     Copyright 2013, Frank Förster (http://frankfoerster.com)
 * @link          http://github.com/frankfoerster/wasabi
 * @package       Wasabi
 * @subpackage    Wasabi.Plugin.Core.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CoreAppModel', 'Core.Model');
App::uses('Hash', 'Utility');

/**
 * @property AttachedMedia $AttachedMedia
 */
class Media extends CoreAppModel {

//	/**
//	 * Other options
//	 *
//	 * @var array
//	 */
//	protected $_options = array(
//		'maxFileSize' => null,
//		'minFileSize' => 1,
//		'extensions' => array(
//			'bmp', 'gif', 'jpg', 'jpeg', 'png', 'svg', 'ico', 'avi', 'mp4', 'fvl', '3gp', 'pdf', 'ps', 'zip', 'rar'
//		),
//		'minHeight' => 0,
//		'maxHeight' => 0,
//		'minWidth' => 0,
//		'maxWidth' => 0
//	);

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'Core.User'
		)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'AttachedMedia' => array(
			'className' => 'Core.AttachedMedia',
			'dependent' => true
		)
	);

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
//		'general' => array(
//			'phpSizeLimit' => array(
//				'rule' => 'inUnderPhpSizeLimit',
//				'message' => 'This file exceeds the maximum file size.'
//			),
			# isUnderFormSizeLimit
			# isCompletedUpload
			# isFileUpload
//			'tmpDirExists' => array(
//				'rule' => 'tempDirExists',
//				'message' => 'The temporary upload directoy is missing.'
//			),
			# isSuccessfulWrite
			# notStoppedByPhpExtension
//		),
		'mime_type' => array(
			'allowedMimeType' => array(
				'rule' => 'isAllowedMimetype',
				'message' => 'The mimetype "{1}" of File "{0}" is not allowed.'
			)
			# isAboveMinSize
			# isBelowMaxSize
			# isUploadDirPresent
			# isUploadDirWritable
		),
		'ext' => array(
			'hasExtension' => array(
				'rule' => 'notEmpty',
				'message' => 'The File "{0}" must have an extension.',
				'last' => true
			),
			'allowedExtension' => array(
				'rule' => 'isAllowedExtension',
				'message' => 'The extension ".{1}" of File "{0}" is not allowed.'
			)
		)//,
//		'imageChecks' => array(
			# isAboveMinHeight
			# isBelowMaxHeight
			# isAboveMinWidth
			# isBelowMaxWidth
//		)
	);

	/**
	 * beforeValidate callback
	 *
	 * @param array $options
	 * @return boolean
	 * @TODO: Detect mime type from tmp file if it is not set!!!
	 */
	public function beforeValidate($options = array()) {
		if (isset($this->data[$this->alias]['tmp_name'])) {
			if (isset($this->data[$this->alias]['name'])) {
				$parts = explode('.', $this->data[$this->alias]['name']);
				$this->data[$this->alias]['ext'] = strtolower(array_pop($parts));
				$this->data[$this->alias]['name'] = join('.', $parts);
				$this->data[$this->alias]['fullname'] =
					$this->data[$this->alias]['name'] . '.' . $this->data[$this->alias]['ext'];
			}
			if (isset($this->data[$this->alias]['type'])) {
				$this->data[$this->alias]['mime_type'] = $this->data[$this->alias]['type'];
				$m = $this->data[$this->alias]['mime_type'];
				App::uses('Mime', 'Core.Lib');
				$mime = Mime::getInstance();
				switch (true) {
					case in_array($m, $mime->getTypes('image')):
						$type = 'image';
						break;
					case in_array($m, $mime->getTypes('video')):
						$type = 'video';
						break;
					case in_array($m, $mime->getTypes('audio')):
						$type = 'audio';
						break;
					case in_array($m, $mime->getTypes('document')):
						$type = 'document';
						break;
					default:
						$type = 'other';
						break;
				}
				$this->data[$this->alias]['type'] = $type;
			} else {
				$this->data[$this->alias]['type'] = 'other';
			}
		}

		return parent::beforeValidate($options);
	}

	public function beforeSave($options = array()) {
		$this->_updateFolderNames();
	}

	/**
	 * afterSave callback
	 *
	 * @param boolean $created
	 */
	public function afterSave($created) {
		$this->_handleUploadedFile();
	}

	/**
	 * Validate a submitted file and return
	 * its validation errors.
	 *
	 * @param array $file
	 * @return array
	 */
	public function validateFile($file) {
		$errors = array();
		$this->create($file);

		if (!$this->validates()) {
			foreach ($this->validationErrors as $field => $fieldErrors) {
				foreach ($fieldErrors as $fieldError) {
					$errors[] = array(
						'message' => $fieldError,
						'context' => array(
							$file['name'],
							$this->data[$this->alias][$field]
						)
					);
				}
			}
		}

		return $errors;
	}

	public function getTypeCounts($options = array()) {
		$typeCounts = $this->find('all', array_merge($options, array(
			'fields' => array('Media.type', 'COUNT(*) AS type_count'),
			'group' => 'type',
			'related' => array(
				'User' => array('fields' => array('User.id'))
			)
		)));
		$typeCounts = Hash::combine($typeCounts, '{n}.Media.type', '{n}.0.type_count');
		$typeCounts['all'] = array_sum(array_values($typeCounts));
		return $typeCounts;
	}

	protected function _handleUploadedFile() {
		new Folder(APP . WEBROOT_DIR . DS . $this->data[$this->alias]['upload_dir'], true, 0755);
		move_uploaded_file($this->data[$this->alias]['tmp_name'], APP . WEBROOT_DIR . DS . $this->data[$this->alias]['upload_path']);
	}

	protected function _updateFolderNames() {
		$uploadDir = Configure::read('Settings.Core.Media.upload_directory');
		$subDir = Configure::read('Settings.Core.Media.upload_subdirectories');
		if ($subDir !== 'none') {
			$subDir = date($subDir);
			$subDir = preg_replace('/{{DS}}/', DS, $subDir);
			$uploadDir = $uploadDir . DS . $subDir;
		}
		$this->data[$this->alias]['upload_dir'] = $uploadDir;
		$this->data[$this->alias]['upload_path'] = $uploadDir . DS . $this->data[$this->alias]['fullname'];

		$name = $this->data[$this->alias]['name'];
		$i = 1;
		while (file_exists(APP . $this->data[$this->alias]['upload_path'])) {
			$this->data[$this->alias]['name'] = $name . $i++;
			$this->data[$this->alias]['fullname'] = $this->data[$this->alias]['name'] . '.' . $this->data[$this->alias]['ext'];
			$this->data[$this->alias]['upload_path'] = $uploadDir . DS . $this->data[$this->alias]['fullname'];
		}
	}

	/**
	 * Check that the file does not exceed the max
	 * file size specified by PHP
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function isUnderPhpSizeLimit($check) {
//		return $check['error'] !== UPLOAD_ERR_INI_SIZE;
//	}

	/**
	 * Check that the file does not exceed the max
	 * files size specified in the Form via the hidden
	 * input MAX_FILE_SIZE e.g.:
	 * <input name="MAX_FILE_SIZE" value="1048576" type="hidden">
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function isUnderFormSizeLimit($check) {
//		return $check['error'] !== UPLOAD_ERR_FORM_SIZE;
//	}

	/**
	 * Check that the file was completely uploaded.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function isCompletedUpload($check) {
//		return $check['error'] !== UPLOAD_ERR_PARTIAL;
//	}

	/**
	 * Check that a file was uploaded.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function isFileUpload($check) {
//		return $check['error'] !== UPLOAD_ERR_NO_FILE;
//	}

	/**
	 * Check if the PHP tmp directory is missing.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function tempDirExists($check) {
//		return $check['error'] !== UPLOAD_ERR_NO_TMP_DIR;
//	}

	/**
	 * Check that the file was successfully written to the server.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function isSuccessfulWrite($check) {
//		return $check['error'] !== UPLOAD_ERR_CANT_WRITE;
//	}

	/**
	 * Check that the file upload was not stopped by a PHP extension.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function notStoppedByPhpExtension($check) {
//		return $check['error'] !== UPLOAD_ERR_EXTENSION;
//	}

	/**
	 * Check if the file is of a allowed mimetype.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isAllowedMimetype($check) {
		if (Configure::read('Settings.Core.Media.allow_all_mime_types') === '1') {
			return true;
		}

		$field = $this->_getField($check);

		return in_array($check[$field], Configure::read('Settings.Core.Media.allowed_mime_types'));
	}

	/**
	 * Check if the file extensions is allowed.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isAllowedExtension($check) {
		if (Configure::read('Settings.Core.Media.allow_all_file_extensions') === '1') {
			return true;
		}

		$field = $this->_getField($check);

		return in_array($check[$field], Configure::read('Settings.Core.Media.allowed_file_extensions'));
	}

	/**
	 * Check that the upload directory is writable.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function isUploadDirWritable($check) {
//		return is_writable($check['abs_path']);
//	}

	/**
	 * Check if the upload directory exists.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function isUploadDirPresent($check) {
//		return is_dir($check['abs_path']);
//	}

	/**
	 * Check if the file is below the maximum file upload size.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function isBelowMaxSize($check) {
//		if (!isset($check['size'])) {
//			return false;
//		}
//
//		if ($this->_options['maxFileSize'] === null) {
//			return true;
//		}
//
//		return $check['size'] <= $this->_options['maxFileSize'];
//	}

	/**
	 * Check if the file is above the minimum file upload size.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function isAboveMinSize($check) {
//		if (!isset($check['size'])) {
//			return false;
//		}
//
//		return $check['size'] >= $this->_options['minFileSize'];
//	}

	/**
	 * Check that the file is above the minimum height requirement.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function isAboveMinHeight($check) {
//		if (!isset($check['tmp_name'])) {
//			return false;
//		}
//
//		list(, $imgHeight) = getimagesize($check['tmp_name']);
//
//		return $imgHeight >= $this->_options['minHeight'];
//	}

	/**
	 * Check that the file is below the maximum height requirement.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function isBelowMaxHeight($check) {
//		if (!isset($check['tmp_name'])) {
//			return false;
//		}
//
//		if ($this->_options['maxHeight'] === 0) {
//			return true;
//		}
//
//		list(, $imgHeight) = getimagesize($check['tmp_name']);
//
//		return $imgHeight <= $this->_options['maxHeight'];
//	}

	/**
	 * Check that the file is above the minimum width requirement.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function isAboveMinWidth($check) {
//		if (!isset($check['tmp_name'])) {
//			return false;
//		}
//
//		list($imgWidth, ) = getimagesize($check['tmp_name']);
//
//		return $imgWidth >= $this->_options['minWidth'];
//	}

	/**
	 * Check that the file is below the maximum width requirement.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
//	public function isBelowMaxWidth($check) {
//
//		if (!isset($check['tmp_name'])) {
//			return false;
//		}
//
//		if ($this->_options['maxWidth'] === 0) {
//			return true;
//		}
//
//		list($imgWidth, ) = getimagesize($check['tmp_name']);
//
//		return $imgWidth <= $this->_options['maxWidth'];
//	}

	/**
	 * Get the key of the single field that
	 * is currently checked.
	 *
	 * @param mixed $check
	 * @return mixed
	 */
	protected function _getField($check) {
		$keys = array_keys($check);
		return array_pop($keys);
	}
}
