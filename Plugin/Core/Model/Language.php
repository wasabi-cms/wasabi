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

App::uses('Cache', 'Cache');
App::uses('CakeSession', 'Model/Datasource');

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
		),
		'available_at_frontend' => array(
			'at_least_one' => array(
				'rule' => 'atLeastOneFrontendLanguageIsAvailable',
				'message' => 'At least one language must be available at frontend.',
				'allowEmpty' => true
			)
		)
	);

	/**
	 * afterSave callback
	 * Clear the languages cache whenever a new language is created
	 * or an existing language is updated.
	 *
	 * @param bool $created
	 * @return void
	 */
	public function afterSave($created) {
		Cache::delete('languages', 'core.infinite');
	}

	/**
	 * beforeDelete callback
	 * 1. Clear the Wasabi.content_language_id session the language with this id has been deleted.
	 * 2. If no language is available at frontend after deletion, pick the next language by position
	 * and make it available at frontend.
	 * 3. Clear the language cache.
	 *
	 * @return void
	 */
	public function afterDelete() {
		$sessKey = 'Wasabi.content_language_id';
		if (CakeSession::check($sessKey)) {
			$lang = $this->find('first', array(
				'conditions' => array(
					$this->alias . '.' . $this->primaryKey => CakeSession::read($sessKey)
				)
			));
			if (!$lang) {
				CakeSession::delete($sessKey);
			}
		}
		if (!$this->atLeastOneFrontendLanguageIsAvailable(null, false, $this->id)) {
			$lang = $this->find('first');
			$data = array(
				'id' => $lang['Language']['id'],
				'available_at_frontend' => true
			);
			$this->save($data);
		}
		Cache::delete('languages', 'core.infinite');
	}

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
	 * Check if a language by given $id can be deleted,
	 *
	 * @param integer $id
	 * @return bool
	 */
	public function canBeDeleted($id) {
		// available backend languages cannot be deleted
		if ($id == 1 || $id == 2) {
			return false;
		}

		// language with $id does not exist
		if (!$this->exists($id)) {
			return false;
		}

		return true;
	}

	/**
	 * Check if at least one other frontend language is available.
	 *
	 * @param array $check the validation field and its value
	 * @param bool $validation determines if this method was called from model validations
	 * @param null|integer $id either null on validation or the language id to check against
	 * @throws InvalidArgumentException
	 * @return bool
	 */
	public function atLeastOneFrontendLanguageIsAvailable($check = array(), $validation = true, $id = null) {
		if ($validation) {
			$needsCheck = isset($this->data[$this->alias][$this->primaryKey]) && !$check['available_at_frontend'];
			if (!$needsCheck) {
				return true;
			}
			$id = $this->data[$this->alias][$this->primaryKey];
		}

		if ($id === null) {
			throw new InvalidArgumentException(__d('core', 'You have to provide an $id to check against.'));
		}

		$availableAtFrontendCount = $this->find('count', array(
			'conditions' => array(
				$this->alias . '.' . $this->primaryKey . ' <>' => $id,
				$this->alias . '.available_at_frontend' => true
			)
		));

		return $availableAtFrontendCount >= 1;
	}

}
