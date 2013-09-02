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

/**
 * @method boolean|array saveKeyValues(array $data) inherited from KeyValueBehavior
 */
class Setting extends CoreAppModel {

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
	 * afterSave callback
	 * Clear the settings cache whenever the settings are updated
	 * and notify all plugins via an event 'Backend.Core.CoreSettings.changed'
	 *
	 * @param bool $created
	 * @return void
	 */
	public function afterSave($created) {
		Cache::delete('settings', 'core.infinite');
		WasabiEventManager::trigger(new stdClass(), 'Common.Settings.changed');

		parent::afterSave($created);
	}

}
