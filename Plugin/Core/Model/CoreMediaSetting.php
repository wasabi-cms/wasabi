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
			'%Y' => __d('core', '%s (year only)', array($y)),
			'%Y/%m' => __d('core', '%s/%s (year/month)', array($y, $m)),
			'%Y_%m' => __d('core', '%s_%s (year_month)', array($y, $m)),
			'%Y/%m/%d' => __d('core', '%s/%s/%s (year/month/day)', array($y, $m, $d)),
			'%Y_%m_%d' => __d('core', '%s_%s_%s (year_month_day)', array($y, $m, $d))
		);
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
