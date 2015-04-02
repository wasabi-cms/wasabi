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
	const PAGE_KEY = 'page_id';
	const LANG_KEY = 'language_id';

	public $belongsTo = array(
		'CmsPage' => array(
			'className' => 'Cms.CmsPage',
			'foreignKey' => self::PAGE_KEY
		)
	);

	public $validate = array(
		'url' => array(
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'This url is not available.'
			)
		)
	);

	public function afterSave($created = null) {
//		if ($created) {
//			$route = $this->data['Route'];
//		} else {
//			$route = $this->findById($this->id);
//			$route = $route['Route'];
//		}
//		$identifier = $route['model'] . '__' . $route['model_id'] . '__' . $route['language_id'];
//		Cache::delete($identifier, 'core.routes');
	}

	public function getRouteTypes() {
		$routeTypes = array(
			self::TYPE_DEFAULT_ROUTE,
			self::TYPE_REDIRECT_ROUTE
		);
		$routeTypes = array_combine(array_values($routeTypes), $routeTypes);
		return $routeTypes;
	}

	public static function instance() {
		static $instance;
		if (!$instance) {
			$instance = ClassRegistry::init('Core.Route');
		}
		return $instance;
	}

	public static function getCacheKey($route) {

	}

}
