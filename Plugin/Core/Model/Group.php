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

class Group extends CoreAppModel {

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'User' => array(
			'className' => 'Core.User'
		)
	);

	/**
	 * Find all groups with find $options
	 *
	 * @param array $options
	 * @return array
	 */
	public function findAll($options = array()) {
		return $this->find('all', $options);
	}

	/**
	 * Find a single group by id
	 *
	 * @param $id
	 * @param array $options
	 * @return array
	 */
	public function findById($id, $options = array()) {
		$opts['conditions'] = array(
			$this->alias . '.id' => (int) $id
		);
		return $this->find('first', Hash::merge($options, $opts));
	}

}
