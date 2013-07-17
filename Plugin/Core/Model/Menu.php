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
 * @property MenuItem $MenuItem
 */
class Menu extends CoreAppModel {

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'MenuItem' => array(
			'className' => 'Core.MenuItem'
		)
	);

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please give this menu a name.'
			)
		)
	);

	/**
	 * Find all menus with find $options
	 *
	 * @param array $options
	 * @return array
	 */
	public function findAll($options = array()) {
		return $this->find('all', $options);
	}

	/**
	 * Find a single menu by id
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

	public function findWithMenuItemsById($id, $options = array()) {
		$opts['conditions'] = array(
			$this->alias . '.id' => (int) $id
		);
		$opts['related'] = array(
			'MenuItem'
		);
		$result = $this->find('first', Hash::merge($options, $opts));
		if (!isset($result['Menu']['MenuItems'])) {
			return $result;
		}

		$result['MenuItem'] = $result['Menu']['MenuItems'];
		unset($result['Menu']['MenuItems']);
		$result['MenuItem'] = Hash::extract($result['MenuItem'], '{n}.MenuItem');

		return $result;
	}

}
