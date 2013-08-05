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

class Route extends CoreAppModel {

	const TYPE_DEFAULT_ROUTE  = 'Default Route';
	const TYPE_REDIRECT_ROUTE = 'Redirect';

	public $validate = array(
		'url' => array(
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'This url is not available.'
			)
		)
	);

	public function afterSave($created = null) {
		// clear cache
	}

	public function afterDelete() {
		// clear cache
	}

	public function getRouteTypes() {
		$reflector = new ReflectionClass(__CLASS__);
		$constants = $reflector->getConstants();

		$types = array();
		foreach ($constants as $type) {
			$types[$type] = $type;
		}

		return $types;
	}

}
