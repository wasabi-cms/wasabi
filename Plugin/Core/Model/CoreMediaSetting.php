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

App::uses('Setting', 'Core.Model');

class CoreMediaSetting extends Setting {

	/**
	 * This model uses the 'settings' db table.
	 *
	 * @var string
	 */
	public $useTable = 'settings';

	/**
	 * Behaviors attached to this model.
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Core.KeyValue' => array(
			'scope' => 'Core',
			'serializeFields' => array(
				'Media__allowed_mime_types',
				'Media__allowed_file_extensions'
			)
		)
	);

	/**
	 * Holds all select options for Media__upload_subdirectories.
	 *
	 * @var array
	 */
	public $subDirectories = array();

	/**
	 * Supported image files.
	 * (ext => mime type)
	 *
	 * @var array
	 */
	public $imageFiles = array(
		'bmp'      => 'image/bmp',
		'gif'      => 'image/gif',
		'jpg|jpeg' => 'image/jpeg',
		'png'      => 'image/png',
		'psd'      => 'image/psd',
		'svg'      => 'image/svg+xml',
		'tif|tiff' => 'image/tiff',
		'ico'      => 'image/vnd.microsoft.icon|image/x-icon'
	);

	/**
	 * Supported audio files.
	 * (ext => mime type)
	 *
	 * @var array
	 */
	public $audioFiles = array(
		'mid|midi'    => 'audio/midi',
		'mp3|m4a|m4b' => 'audio/mpeg',
		'oga|ogg'     => 'audio/ogg',
		'wav'         => 'audio/wav',
		'wma'         => 'audio/x-ms-wma',
		'ra|ram'      => 'audio/x-realaudio'
	);

	/**
	 * Supported video files.
	 * (ext => mime type)
	 *
	 * @var array
	 */
	public $videoFiles = array(
		'3gp'          => 'video/3gpp',
		'avi'          => 'video/avi',
		'divx'         => 'video/divx',
		'mp4|m4v'      => 'video/mp4',
		'mpeg|mpe|mpg' => 'video/mpeg',
		'ogv'          => 'video/ogg',
		'mov|qt'       => 'video/quicktime',
		'webm'         => 'video/webm',
		'flv'          => 'video/x-flv',
		'mkv'          => 'video/x-matroska',
		'asf|asx'      => 'video/x-ms-asf',
		'wmv'          => 'video/x-ms-wmv'
	);

	/**
	 * Supported document files.
	 * (ext => mime type)
	 *
	 * @var array
	 */
	public $documentFiles = array(
		'doc' => 'application/msword',
		'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote',
		'pdf' => 'application/pdf',
		'ps'  => 'application/postscript',
		'rtf' => 'application/rtf',
		'mdb' => 'application/vnd.ms-access',
		'xla|xls|xlt|xlw' => 'application/vnd.ms-excel',
		'pot|pps|ppt'     => 'application/vnd.ms-powerpoint',
		'mpp' => 'application/vnd.ms-project',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
	);

	/**
	 * Other files.
	 * (ext => mime type)
	 *
	 * @var array
	 */
	public $otherFiles = array(
		'rar' => 'application/rar|application/x-rar-compressed',
		'7z'  => 'application/x-7z-compressed',
		'gz|gzip' => 'application/x-gzip',
		'exe' => 'application/x-msdownload',
		'tar' => 'application/x-tar',
		'swf' => 'application/x-shockwave-flash',
		'zip' => 'application/zip'
	);

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'Media__upload_directory' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a name for the upload directory.'
			)
		),
		'Media__upload_subdirectories' => array(
			'validSelection' => array(
				'rule' => array('isValidSubdirectory'),
				'message' => 'Please choose on of the above options.'
			)
		)
	);

	/**
	 * Constructor
	 *
	 * Set up available sub directories.
	 *
	 * @param array|boolean|integer|string $id
	 * @param string $table
	 * @param string $ds
	 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$y = date('Y');
		$m = date('m');
		$d = date('d');

		$this->subDirectories = array(
			'none' => __d('core', 'No'),
			'Y' => __d('core', '%s (year only)', array($y)),
			'Y{{DS}}m' => __d('core', '%s/%s (year/month)', array($y, $m)),
			'Y_m' => __d('core', '%s_%s (year_month)', array($y, $m)),
			'Y-m' => __d('core', '%s-%s (year-month)', array($y, $m)),
			'Y{{DS}}m{{DS}}d' => __d('core', '%s/%s/%s (year/month/day)', array($y, $m, $d)),
			'Y_m_d' => __d('core', '%s_%s_%s (year_month_day)', array($y, $m, $d)),
			'Y-m-d' => __d('core', '%s-%s-%s (year-month-day)', array($y, $m, $d))
		);
	}

	/**
	 * beforeSaveKeyValues callback
	 * Gets called before saveKeyValues on the KeyValue behavior.
	 *
	 * Unset allowed mime types / file extensions if their corresponding "all"
	 * checkbox is checked.
	 *
	 * @param array $data
	 * @return array
	 */
	public function beforeSaveKeyValues($data = array()) {
		if (isset($data[$this->alias]['Media__allow_all_mime_types']) &&
			$data[$this->alias]['Media__allow_all_mime_types'] === '1'
		) {
			unset($data[$this->alias]['Media__allowed_mime_types']);
		}

		if (isset($data[$this->alias]['Media__allow_all_file_extensions']) &&
			$data[$this->alias]['Media__allow_all_file_extensions'] === '1'
		) {
			unset($data[$this->alias]['Media__allowed_file_extensions']);
		}

		return $data;
	}

	/**
	 * Make sure a valid subdirectory template is selected.
	 *
	 * @param array $check
	 * @return boolean
	 */
	public function isValidSubdirectory($check) {
		$keys = array_keys($this->subDirectories);
		return in_array($check['Media__upload_subdirectories'], $keys);
	}

	/**
	 * Extract extensions from the $files array keys.
	 * and sort the results.
	 *
	 * @param array $files
	 * @param boolean $forSelect if true, the resulting array values will be copied to their keys
	 * @return array
	 */
	public function getExtensions($files, $forSelect = true) {
		$ext = array();
		$extractedExt = array_keys($files);
		foreach ($extractedExt as $e) {
			$e = explode('|', $e);
			$ext = array_merge($ext, $e);
		}
		sort($ext, SORT_STRING);
		return ($forSelect === true) ? array_combine($ext, $ext) : $ext;
	}

	/**
	 * Extract mime types from the $files array values.
	 *
	 * @param array $files
	 * @param boolean $forSelect if true, the resulting array values will be copied to their keys
	 * @return array
	 */
	public function getMimeTypes($files, $forSelect = true) {
		$mimeTypes = array();
		$extractedMimeTypes = array_values($files);
		foreach ($extractedMimeTypes as $m) {
			$m = explode('|', $m);
			$mimeTypes = array_merge($mimeTypes, $m);
		}
		sort($mimeTypes, SORT_STRING);
		return ($forSelect === true) ? array_combine($mimeTypes, $mimeTypes) : $mimeTypes;
	}
}
