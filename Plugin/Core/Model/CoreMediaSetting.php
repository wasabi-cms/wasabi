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
}
