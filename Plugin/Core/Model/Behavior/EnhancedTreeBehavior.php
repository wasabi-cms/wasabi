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
 * @subpackage    Wasabi.Plugin.Core.Model.Behavior
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Hash', 'Utility');
App::uses('TreeBehavior', 'Model/Behavior');

class EnhancedTreeBehavior extends TreeBehavior {

	/**
	 * A convenience method for returning a hierarchical array used for HTML select boxes
	 *
	 * Now contains the $maxDepth options.
	 *
	 * @param Model $Model Model instance
	 * @param string|array $conditions SQL conditions as a string or as an array('field' =>'value',...)
	 * @param string $keyPath A string path to the key, i.e. "{n}.Post.id"
	 * @param string $valuePath A string path to the value, i.e. "{n}.Post.title"
	 * @param string $spacer The character or characters which will be repeated
	 * @param integer $recursive The number of levels deep to fetch associated records
	 * @param integer $maxDepth The maximum depth of the generated tree list
	 * @return array An associative array of records, where the id is the key, and the display field is the value
	 * @link http://book.cakephp.org/2.0/en/core-libraries/behaviors/tree.html#TreeBehavior::generateTreeList
	 */
	public function generateTreeList(Model $Model, $conditions = null, $keyPath = null, $valuePath = null, $spacer = '_', $recursive = null, $maxDepth = null) {
		$overrideRecursive = $recursive;
		extract($this->settings[$Model->alias]);
		if (!is_null($overrideRecursive)) {
			$recursive = $overrideRecursive;
		}

		$fields = null;
		if (!$keyPath && !$valuePath && $Model->hasField($Model->displayField)) {
			$fields = array($Model->primaryKey, $Model->displayField, $left, $right);
		}

		if (!$keyPath) {
			$keyPath = '{n}.' . $Model->alias . '.' . $Model->primaryKey;
		}

		if (!$valuePath) {
			$valuePath = array('%s%s', '{n}.tree_prefix', '{n}.' . $Model->alias . '.' . $Model->displayField);

		} elseif (is_string($valuePath)) {
			$valuePath = array('%s%s', '{n}.tree_prefix', $valuePath);

		} else {
			array_unshift($valuePath, '%s' . $valuePath[0], '{n}.tree_prefix');
		}
		$order = $Model->escapeField($left) . " asc";
		$results = $Model->find('all', compact('conditions', 'fields', 'order', 'recursive'));
		$stack = array();

		foreach ($results as $i => $result) {
			$count = count($stack);
			while ($stack && ($stack[$count - 1] < $result[$Model->alias][$right])) {
				array_pop($stack);
				$count--;
			}
			if ($maxDepth !== null && $count < $maxDepth) {
				$results[$i]['tree_prefix'] = str_repeat($spacer, $count);
			} else {
				unset($results[$i]);
			}
			$stack[] = $result[$Model->alias][$right];
		}
		if (empty($results)) {
			return array();
		}
		return Hash::combine($results, $keyPath, $valuePath);
	}

}