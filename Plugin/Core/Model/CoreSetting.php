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
 * @method boolean|array saveKeyValues(array $data) inherited from KeyValueBehavior
 */
class CoreSetting extends CoreAppModel {

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
			'scope' => 'Core'
		)
	);

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
		),
		'enable_caching' => array(
			'matches' => array(
				'rule' => array('inList', array('0', '1')),
				'message' => 'Invalid cache status selected.'
			)
		),
		'cache_duration' => array(
			'matches' => array(
				'rule' => array('inList', array(
					'1 hour', '2 hours', '4 hours',	'8 hours', '16 hours',
					'1 day', '2 days', '5 days', '7 days', '14 days', '30 days',
					'60 days', '90 days', '180 days', '365 days', '999 days'
				)),
				'message' => 'Invalid cache time selected.'
			)
		)
	);

	/**
	 * afterSave callback
	 * Clear the core_settings cache whenever the settings are updated
	 * and notify all plugins via an event 'Backend.Core.CoreSettings.changed'
	 *
	 * @param bool $created
	 * @return void
	 */
	public function afterSave($created) {
		if (!$created) {
			Cache::delete('core_settings', 'core.infinite');
			WasabiEventManager::trigger(new stdClass(), 'Backend.Core.CoreSettings.changed');
		}
	}

	/**
	 * Retrieve the settings row by id
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
