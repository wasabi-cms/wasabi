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
 * @subpackage    Wasabi.Plugin.Core.Lib
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class Wasabi {

	/**
	 * Hook a component onto a single controller,
	 * or to all controllers via '*'.
	 *
	 * @param string $controllerName
	 * @param string $componentName
	 * @param array  $options
	 */
	public static function hookComponent($controllerName, $componentName, $options = array()) {
		self::hookControllerProperty($controllerName, 'components', array($componentName => $options));
	}

	/**
	 * Hook a behavior onto a single model,
	 * or to all models via '*'.
	 *
	 * @param string $modelName
	 * @param string $behaviorName
	 * @param array $options
	 */
	public static function hookBehavior($modelName, $behaviorName, $options = array()) {
		self::hookModelProperty($modelName, 'actsAs', array($behaviorName => $options));
	}

	/**
	 * Hook a helper onto a single controller,
	 * or to all controllers via '*'.
	 *
	 * @param string $controllerName
	 * @param string $helperName
	 * @param array $options
	 */
	public static function hookHelper($controllerName, $helperName, $options = array()) {
		self::hookControllerProperty($controllerName, 'helpers', array($helperName => $options));
	}

	/**
	 * Hook model property
	 * Useful when models need to be associated to another one, setting behaviors,
	 * disabling cache, etc.
	 *
	 * @param $modelName
	 * @param $property
	 * @param $value
	 */
	public static function hookModelProperty($modelName, $property, $value) {
		$prefix = 'Hook.model_properties';
		self::_hookProperty($prefix, $modelName, $property, $value);
	}

	/**
	 * Hook controller property
	 *
	 * @param $controllerName
	 * @param $property
	 * @param $value
	 */
	public static function hookControllerProperty($controllerName, $property, $value) {
		$prefix = 'Hook.controller_properties';
		self::_hookProperty($prefix, $controllerName, $property, $value);
	}

	/**
	 * Hook a single property.
	 *
	 * @param string $prefix
	 * @param string $name
	 * @param string $property
	 * @param mixed $value
	 */
	protected static function _hookProperty($prefix, $name, $property, $value) {
		$propertyValue = Configure::read($prefix . '.' . $name . '.' . $property);
		if (is_array($propertyValue)) {
			if (is_array($value)) {
				$propertyValue = Hash::merge($propertyValue, $value);
			} else {
				$propertyValue = $value;
			}
		} else {
			$propertyValue = $value;
		}
		Configure::write($prefix . '.' . $name . '.' . $property, $propertyValue);
	}

	/**
	 * Applies properties set from hooks to an object in __construct().
	 *
	 * @param string $configKey
	 * @param mixed $object
	 */
	public static function applyHookProperties($configKey, &$object = null) {
		if (empty($object)) {
			$object = self;
		}
		$objectName = empty($object->name) ? get_class($object) : $object->name;
		$hookProperties = Configure::read($configKey . '.' . $objectName);
		$globalHookProperties = Configure::read($configKey . '.*');
		if (is_array($globalHookProperties)) {
			$hookProperties = Hash::merge($globalHookProperties, $hookProperties);
			unset($globalHookProperties);
		}
		if (is_array($hookProperties)) {
			foreach ($hookProperties as $property => $value) {
				if (!isset($object->$property)) {
					$object->$property = $value;
				} else {
					if (is_array($object->$property)) {
						if (is_array($value)) {
							$object->$property = Hash::merge($object->$property, $value);
						} else {
							$object->$property = $value;
						}
					} else {
						$object->$property = $value;
					}
				}
			}
		}
	}

}
