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

class CoreCacheSetting extends Setting {

	/**
	 * This model uses the 'settings' db table.
	 *
	 * @var string
	 */
	public $useTable = 'settings';

	/**
	 * Holds all select options for cache_duration.
	 *
	 * @var array
	 */
	public $cacheDurations = array();

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'enable_caching' => array(
			'matches' => array(
				'rule' => array('inList', array('0', '1')),
				'message' => 'Invalid cache status selected.'
			)
		),
		'cache_duration' => array(
			'matches' => array(
				'rule' => array('isValidCacheDuration'),
				'message' => 'Invalid cache time selected.'
			)
		)
	);

	/**
	 * Constructor
	 *
	 * Set up available cache durations.
	 *
	 * @param array|boolean|integer|string $id
	 * @param string $table
	 * @param string $ds
	 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->cacheDurations = array(
			'1 hour' => __d('core', '1 hour'),
			'2 hours' => __d('core', '%s hours', array(2)),
			'4 hours' => __d('core', '%s hours', array(4)),
			'8 hours' => __d('core', '%s hours', array(8)),
			'16 hours' => __d('core', '%s hours', array(16)),
			'1 day' => __d('core', '1 day'),
			'2 days' => __d('core', '%s days', array(2)),
			'5 days' => __d('core', '%s days', array(5)),
			'7 days' => __d('core', '%s days', array(7)),
			'14 days' => __d('core', '%s days', array(14)),
			'30 days' => __d('core', '%s days', array(30)),
			'60 days' => __d('core', '%s days', array(60)),
			'90 days' => __d('core', '%s days', array(90)),
			'180 days' => __d('core', '%s days', array(180)),
			'365 days' => __d('core', '%s days', array(365)),
			'999 days' => __d('core', '%s days', array(999))
		);
	}

	/**
	 * Make sure the cache duration is available.
	 *
	 * @param array $check
	 * @return boolean
	 */
	public function isValidCacheDuration($check) {
		$keys = array_keys($this->cacheDurations);
		return in_array($check['cache_duration'], $keys);
	}

}
