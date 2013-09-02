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

class CoreGeneralSetting extends Setting {

	/**
	 * This model uses the 'settings' db table.
	 *
	 * @var string
	 */
	public $useTable = 'settings';

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'application_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a name for your application.'
			)
		)
	);

	/**
	 * beforeSave callback
	 *
	 * Strip unallowed tags from 'Login__Message__text'.
	 *
	 * @param array $options
	 * @return boolean
	 */
	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]) &&
			$this->data[$this->alias]['key'] === 'Login__Message__text'
		) {
			$allowed = '<b><strong><a><br>';
			$this->data[$this->alias]['value'] = strip_tags($this->data[$this->alias]['value'], $allowed);
		}

		return parent::beforeSave();
	}
}
