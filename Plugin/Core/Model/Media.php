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

class Media extends CoreAppModel {

	/**
	 * Supported image mimetypes
	 *
	 * @var array
	 */
	protected $_imageMimetypes = array(
		'image/bmp',
		'image/gif',
		'image/jpeg',
		'image/pjpeg',
		'image/png',
		'image/svg+xml',
		'image/vnd.microsoft.icon',
		'image/x-icon'
	);

	/**
	 * Supported video mimetypes
	 *
	 * @var array
	 */
	protected $_videoMimetypes = array(
		'video/x-msvideo',
		'video/mp4',
		'video/x-flv',
		'video/3gpp'
	);

	/**
	 * Supported media mimetypes
	 *
	 * @var array
	 */
	protected $_mediaMimetypes = array(
		'application/pdf',
		'application/postscript',
		'application/zip',
		'application/x-rar-compressed',
	);

	/**
	 * Other options
	 *
	 * @var array
	 */
	protected $_options = array(
		'maxFileSize' => null,
		'minFileSize' => 1,
		'allowedMimetypes' => array('*'),
		'extensions' => array(
			'bmp', 'gif', 'jpg', 'jpeg', 'png', 'svg', 'ico', 'avi', 'mp4', 'fvl', '3gp', 'pdf', 'ps', 'zip', 'rar'
		),
		'minHeight' => 0,
		'maxHeight' => 0,
		'minWidth' => 0,
		'maxWidth' => 0
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

	);

	protected function _handleUploadedFile() {

	}

	/**
	 * Check that the file does not exceed the max
	 * file size specified by PHP
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isUnderPhpSizeLimit($check) {
		$field = $this->_getField($check);

		return $check[$field]['error'] !== UPLOAD_ERR_INI_SIZE;
	}

	/**
	 * Check that the file does not exceed the max
	 * files size specified in the Form via the hidden
	 * input MAX_FILE_SIZE e.g.:
	 * <input name="MAX_FILE_SIZE" value="1048576" type="hidden">
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isUnderFormSizeLimit($check) {
		$field = $this->_getField($check);

		return $check[$field]['error'] !== UPLOAD_ERR_FORM_SIZE;
	}

	/**
	 * Check that the file was completely uploaded.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isCompletedUpload($check) {
		$field = $this->_getField($check);

		return $check[$field]['error'] !== UPLOAD_ERR_PARTIAL;
	}

	/**
	 * Check that a file was uploaded.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isFileUpload($check) {
		$field = $this->_getField($check);

		return $check[$field]['error'] !== UPLOAD_ERR_NO_FILE;
	}

	/**
	 * Check if the PHP tmp directory is missing.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function tempDirExists($check) {
		$field = $this->_getField($check);

		return $check[$field]['error'] !== UPLOAD_ERR_NO_TMP_DIR;
	}

	/**
	 * Check that the file was successfully written to the server.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isSuccessfulWrite($check) {
		$field = $this->_getField($check);

		return $check[$field]['error'] !== UPLOAD_ERR_CANT_WRITE;
	}

	/**
	 * Check that the file upload was not stopped by a PHP extension.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function notStoppedByPhpExtension($check) {
		$field = $this->_getField($check);

		return $check[$field]['error'] !== UPLOAD_ERR_EXTENSION;
	}

	/**
	 * Check that the file is of a supported mimetype.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isSupportedMimetype($check) {
		$field = $this->_getField($check);

		return in_array($check[$field]['mimetype'], array_merge($this->_imageMimetypes, $this->_videoMimetypes, $this->_mediaMimetypes));
	}

	/**
	 * Check if the file is of a allowed mimetype.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isAllowedMimetype($check) {
		$field = $this->_getField($check);

		if (in_array('*', $this->_options['allowedMimetypes'])) {
			return true;
		}

		return in_array($check[$field]['mimetype'], $this->_options['allowedMimetypes']);
	}

	/**
	 * Check that the upload directory is writable.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isUploadDirWritable($check) {
		$field = $this->_getField($check);

		return is_writable($check[$field]['abs_path']);
	}

	/**
	 * Check if the upload directory exists.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isUploadDirPresent($check) {
		$field = $this->_getField($check);

		return is_dir($check[$field]['abs_path']);
	}

	/**
	 * Check if the file is below the maximum file upload size.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isBelowMaxSize($check) {
		$field = $this->_getField($check);

		if (!isset($check[$field]['size'])) {
			return false;
		}

		if ($this->_options['maxFileSize'] === null) {
			return true;
		}

		return $check[$field]['size'] <= $this->_options['maxFileSize'];
	}

	/**
	 * Check if the file is above the minimum file upload size.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isAboveMinSize($check) {
		$field = $this->_getField($check);

		if (!isset($check[$field]['size'])) {
			return false;
		}

		return $check[$field]['size'] >= $this->_options['minFileSize'];
	}

	/**
	 * Check if the file has a valid extension.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isValidExtension($check) {
		$field = $this->_getField($check);

		if (!isset($check[$field]['ext'])) {
			return false;
		}

		return in_array($check[$field]['ext'], $this->_options['extensions']);
	}

	/**
	 * Check that the file is above the minimum height requirement.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isAboveMinHeight($check) {
		$field = $this->_getField($check);

		if (!isset($check[$field]['tmp_name'])) {
			return false;
		}

		list(, $imgHeight) = getimagesize($check[$field]['tmp_name']);

		return $imgHeight >= $this->_options['minHeight'];
	}

	/**
	 * Check that the file is below the maximum height requirement.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isBelowMaxHeight($check) {
		$field = $this->_getField($check);

		if (!isset($check[$field]['tmp_name'])) {
			return false;
		}

		if ($this->_options['maxHeight'] === 0) {
			return true;
		}

		list(, $imgHeight) = getimagesize($check[$field]['tmp_name']);

		return $imgHeight <= $this->_options['maxHeight'];
	}

	/**
	 * Check that the file is above the minimum width requirement.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isAboveMinWidth($check) {
		$field = $this->_getField($check);

		if (!isset($check[$field]['tmp_name'])) {
			return false;
		}

		list($imgWidth, ) = getimagesize($check[$field]['tmp_name']);

		return $imgWidth >= $this->_options['minWidth'];
	}

	/**
	 * Check that the file is below the maximum width requirement.
	 *
	 * @param mixed $check
	 * @return boolean
	 */
	public function isBelowMaxWidth($check) {
		$field = $this->_getField($check);

		if (!isset($check[$field]['tmp_name'])) {
			return false;
		}

		if ($this->_options['maxWidth'] === 0) {
			return true;
		}

		list($imgWidth, ) = getimagesize($check[$field]['tmp_name']);

		return $imgWidth <= $this->_options['maxWidth'];
	}

	/**
	 * Get a single field value
	 *
	 * @param mixed $check
	 * @return mixed
	 */
	protected function _getField($check) {
		$keys = $check;
		return array_pop($keys);
	}
}
