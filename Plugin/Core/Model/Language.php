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

class Language extends CoreAppModel {

	/**
	 * Default order
	 *
	 * @var string
	 */
	public $order = 'Language.position ASC';

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter the name of the language.'
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'message' => 'This language already exists.'
			)
		),
		'locale' => array(
			'length' => array(
				'rule' => array('between', 2, 2),
				'message' => 'Ensure the locale consists of exactly 2 characters.'
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'message' => 'This locale already exists.'
			)
		),
		'iso' => array(
			'length' => array(
				'rule' => array('between', 3, 3),
				'message' => 'Ensure the ISO code consists of exactly 3 characters.'
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'message' => 'This ISO code already exists.'
			)
		),
		'lang' => array(
			'length' => array(
				'rule' => array('between', 2, 5),
				'message' => 'Ensure the HTML language code consists of 2 to 5 characters.'
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'message' => 'This HTML language code already exists.'
			)
		)
	);

	/**
	 * Find all languages with find $options
	 *
	 * @param array $options
	 * @return array
	 */
	public function findAll($options = array()) {
		return $this->find('all', $options);
	}

	/**
	 * Find a single language by id
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

	/**
	 * Check if a user account by given $id can be deleted,
	 *
	 * @param integer $id
	 * @return bool
	 */
	public function canBeDeleted($id) {
		// available backup languages cannot be deleted
		if ($id == 1 || $id == 2) {
			return false;
		}

		// language with $id does not exist
		if (!$this->exists($id)) {
			return false;
		}

		return true;
	}

}
