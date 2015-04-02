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
App::uses('ModelBehavior', 'Model');

class RelatableBehavior extends ModelBehavior {

	/**
	 * Supported association types.
	 *
	 * @var array
	 */
	protected $_assocTypes = array('belongsTo', 'hasOne', 'hasMany');

	/**
	 * Default query params that are filtered out of the relations
	 * while building the relation map.
	 *
	 * @var array
	 */
	protected $_queryParams = array('conditions', 'fields', 'joins', 'order', 'limit', 'offset');

	/**
	 * Holds the behavior settings for each model.
	 *
	 * @var array
	 */
	protected $_settings = array();

	/**
	 * Default query used for supplemental query construction.
	 *
	 * @var array
	 */
	protected $_defaultQuery = array(
		'conditions' => null,
		'fields' => null,
		'joins' => array(),
		'limit' => null,
		'offset' => null,
		'order' => null,
		'page' => 1,
		'group' => null,
		'callbacks' => true
	);

	/**
	 * Holds all relations.
	 *
	 * source -> assocType[] -> target[]
	 *
	 * @var array
	 */
	protected static $_relationMap = array();

	/**
	 * Keeps references to all used models in the find call.
	 *
	 * @var Model[]
	 */
	protected static $_model = array();

	/**
	 * Holds belongsTo queries with results and foreignKeys.
	 * After a belongsTo query has completed its results are stored here
	 * along with all possible foreignKeys for followup hasMany calls.
	 *
	 * @var array
	 */
	protected static $_queries = array();

	/**
	 * Setup the behavior with supplied settings.
	 * At the moment RelatedBehavior does not use any settings.
	 * If settings are needed any time in the future then their defaults can be provided here.
	 *
	 * @param Model $model
	 * @param array $settings
	 * @return void
	 */
	public function setup(Model $model, $settings = array()) {
		$defaults = array();
		$this->_settings[$model->alias] = Hash::merge($defaults, $settings);
	}

	/**
	 * beforeFind callback
	 *
	 * @param Model $model
	 * @param array $query
	 * @return array|bool
	 */
	public function beforeFind(Model $model, $query) {
		if (!isset($query['related']) || empty($query['related'])) {
			return $query;
		}

		self::$_relationMap = $this->_getRelationMap($model, $query['related']);
		unset($query['related']);

		foreach (self::$_relationMap as $source => &$types) {
			foreach ($types as $type => $assocs) {
				if (!in_array($type, array('belongsTo', 'hasOne'))) {
					continue;
				}

				if (!is_object($source)) {
					$source = self::$_model[$source];
				}

				foreach ($assocs as $related) {
					$keys = array_keys(self::$_queries);
					$match = array_values(preg_grep("/(^{$source->alias}___|___{$source->alias}___|___{$source->alias}$)/", $keys));

					if (empty($match)) {
						$relQuery = $this->_mergeQueryParams($this->_defaultQuery, $query);
						$relQuery = $this->_mergeQueryParams($relQuery, $related['query']);
						$isCountQuery = is_string($relQuery['fields']) && strpos($relQuery['fields'], 'COUNT(*) AS ') !== false;
						if (!$isCountQuery) {
							$relQuery = $this->_addFields($source, $relQuery);
							$relQuery = $this->_addFields($source->{$related['name']}, $relQuery);
						}
						$relQuery = $this->_addJoin($source, $source->{$related['name']}, $type, $relQuery);

						self::$_queries["{$source->alias}___{$related['name']}"] = array(
							'query' => $relQuery,
							'results' => array()
						);

						continue;
					}

					self::$_queries[$match[0]]['query'] = $this->_addFields($source, self::$_queries[$match[0]]['query']);
					self::$_queries[$match[0]]['query'] = $this->_addFields($source->{$related['name']}, self::$_queries[$match[0]]['query']);
					self::$_queries[$match[0]]['query'] = $this->_addJoin($source, $source->{$related['name']}, $type, self::$_queries[$match[0]]['query']);

					$newKey = $match[0] . '___' . $related['name'];
					self::$_queries[$newKey] = array(
						'query' => self::$_queries[$match[0]]['query'],
						'results' => array()
					);
					unset(self::$_queries[$match[0]]);
				}

				unset($types[$type]);
			}
		}

		$keys = array_keys(self::$_queries);
		$foundInQueries = preg_grep("/^{$model->alias}___/", $keys);

		if (!empty($foundInQueries)) {
			$query = self::$_queries[$foundInQueries[0]]['query'];
			unset(self::$_queries[$foundInQueries[0]]['query']);
			unset(self::$_queries[$foundInQueries[0]]['results']);
			self::$_queries[$foundInQueries[0]]['base'] = true;
		}

		return $query;
	}

	/**
	 * afterFind callback
	 *
	 * @param Model $model
	 * @param array $results
	 * @param bool $primary
	 * @return array
	 */
	public function afterFind(Model $model, $results, $primary = false) {
		if (!isset(self::$_relationMap[$model->alias])) {
			return $results;
		}

		$foreignKeys = array();

		// check for belongsTo result
		if (!empty($results)) {
			$path = '';
			if (isset($results[0]) && !empty($results[0])) {
				$path = '{n}.';
				$models = array_keys($results[0]);
			} else {
				$models = array_keys($results);
			}
			$lookup = implode('___', $models);

			if (isset(self::$_queries[$lookup])) {
				// belongsTo query found
				if (!isset(self::$_queries[$lookup]['base']) || self::$_queries[$lookup]['base'] !== true) {
					self::$_queries[$lookup]['results'] = $results;
				}
				self::$_queries[$lookup]['foreignKeys'] = array();

				foreach ($models as $m) {
					$source = self::$_model[$m];
					$fkPath = $path . "{$m}.{$source->primaryKey}";
					self::$_queries[$lookup]['foreignKeys'][$m] = array_unique(Hash::extract($results, $fkPath));
				}
			} else {
				$fkPath = $path . "{$model->alias}.{$model->primaryKey}";
				$foreignKeys = array_unique(Hash::extract($results, $fkPath));
			}
		}

		foreach (self::$_relationMap as $source => $relations) {
			foreach ($relations as $type => $targets) {
				if (in_array($type, array('hasMany'))) {
					// look for source in queries
					$keys = array_keys(self::$_queries);
					$sourceQuery = array_values(preg_grep("/(^{$source}___|___{$source}___|___{$source}$)/", $keys));
					if (
						!empty($sourceQuery) &&
						isset(self::$_queries[$sourceQuery[0]]['foreignKeys']) &&
						!empty(self::$_queries[$sourceQuery[0]]['foreignKeys']) &&
						isset(self::$_queries[$sourceQuery[0]]['foreignKeys'][$source])
					) {
						$foreignKeys = self::$_queries[$sourceQuery[0]]['foreignKeys'][$source];
					}
					foreach ($targets as $target) {
						$query = array();
						$pushForeignKeys = false;
						$lookup = null;
						$foundInQueries = array_values(preg_grep("/(^{$target['name']}___|___{$target['name']}___|___{$target['name']}$)/", $keys));
						if (
							!empty($foundInQueries) &&
							$foundInQueries[0] !== "{$source}___{$target['name']}" &&
							$foundInQueries[0] !== "{$target['name']}___{$source}" &&
							isset(self::$_queries[$foundInQueries[0]]['query']) &&
							!empty(self::$_queries[$foundInQueries[0]]['query'])
						) {
							$query = self::$_queries[$foundInQueries[0]]['query'];
							$pushForeignKeys = true;
							$lookup = $foundInQueries[0];
						}
						$linkModel = self::$_model[$target['name']];
						$sourceModel = self::$_model[$source];
						$query['conditions']["{$linkModel->alias}.{$sourceModel->{$type}[$linkModel->alias]['foreignKey']}"] = $foreignKeys;
						unset(self::$_relationMap[$target['name']]);

						$query = $this->_addFields($linkModel, $query);
						$assocResults = $linkModel->find('all', $query);

						if ($pushForeignKeys && !empty($assocResults)) {
							$models = array_keys($assocResults[0]);
							if (isset(self::$_queries[$lookup])) {
								self::$_queries[$lookup]['results'] = $assocResults;
								foreach ($models as $m) {
									$sm = self::$_model[$m];
									$path = "{n}.{$sm->alias}.{$sm->primaryKey}";
									if (!isset(self::$_queries[$lookup]['foreignKeys'])) {
										self::$_queries[$lookup]['foreignKeys'] = array();
									}
									self::$_queries[$lookup]['foreignKeys'][$m] = array_unique(Hash::extract($assocResults, $path));
								}
							}
						}

						if ($lookup !== null) {
							$baseResults = &$results;
							$subResults = &self::$_queries[$lookup]['results'];
						} elseif (
							!empty($sourceQuery) &&
							isset($sourceQuery[0]) &&
							(
								!isset(self::$_queries[$sourceQuery[0]]['base']) ||
								self::$_queries[$sourceQuery[0]]['base'] !== true
							)
						) {
							$baseResults = &self::$_queries[$sourceQuery[0]]['results'];
							$subResults = &$assocResults;
						} else {
							$baseResults = &$results;
							$subResults = &$assocResults;
						}

						foreach ($baseResults as &$r) {
							foreach ($subResults as &$ar) {
								if (!isset($r[$sourceModel->alias][Inflector::pluralize($linkModel->alias)])) {
									$r[$sourceModel->alias][Inflector::pluralize($linkModel->alias)] = array();
								}
								if ($ar[$linkModel->alias][$sourceModel->{$type}[$linkModel->alias]['foreignKey']] === $r[$sourceModel->alias][$sourceModel->primaryKey]) {
									$r[$sourceModel->alias][Inflector::pluralize($linkModel->alias)][] = &$ar;
								}
							}
						}
					}
				}
			}
		}

		self::$_relationMap = self::$_queries = self::$_model = array();

		return $results;
	}

	/**
	 * @param Model $model
	 * @param Model $linkModel
	 * @param string $type
	 * @param array $query
	 * @return array
	 */
	protected function _addJoin(Model $model, Model $linkModel, $type, $query) {
		$joinTable = $this->_getJoinTable($linkModel);

		/** @var DboSource $db */
		$db = $model->getDataSource();

		if ('hasOne' === $type) {
			$conditions = array(
				"{$linkModel->alias}.{$model->{$type}[$linkModel->alias]['foreignKey']}" => $db->identifier("{$model->alias}.id")
			);
		} else {
			$conditions = array(
				"{$model->alias}.{$model->{$type}[$linkModel->alias]['foreignKey']}" => $db->identifier("{$linkModel->alias}.id")
			);
		}

		$query['joins'][] = array(
			'type' => 'LEFT',
			'alias' => $linkModel->alias,
			'table' => $joinTable,
			'conditions' => $conditions
		);

		return $query;
	}

	/**
	 * @param Model $model
	 * @return mixed
	 */
	protected function _getJoinTable(Model $model) {
		if (!isset($this->_settings['join_tables'][$model->alias])) {
			if (!empty($model->tablePrefix)) {
				$tablePrefix = $model->tablePrefix;
			} else {
				$db = $model->getDataSource();
				$tablePrefix = $db->config['prefix'];
			}

			$joinTable = new stdClass();
			$joinTable->tablePrefix = $tablePrefix;
			$joinTable->table = $model->table;
			$joinTable->schemaName = $model->getDataSource()->getSchemaName();

			$this->_settings['join_tables'][$model->alias] = $joinTable;
		}
		return $this->_settings['join_tables'][$model->alias];
	}

	/**
	 * @param Model $model
	 * @param array $query
	 * @return array
	 */
	protected function _addFields(Model $model, $query) {
		if (!isset($query['fields'])) {
			$query['fields'] = array();
		}
		if (is_string($query['fields'])) {
			$query['fields'] = (array) $query['fields'];
		}

		/** @var DboSource $db */
		$db = $model->getDataSource();

		if (empty($query['fields'])) {
			$query['fields'] = $db->fields($model);
			return $query;
		}

		$primaryKeyFound = false;
		$allFields = false;
		$allFieldsPos = 0;
		$fieldFound = false;
		$firstPos = 0;
		foreach ($query['fields'] as $key => &$field) {
			if (preg_match("/^{$model->alias}\.(.*)/", $field, $match) > 0 ||
				preg_match("/^\"{$model->alias}\"\.\"(.*)\"\s/", $field, $match) > 0 ||
				preg_match("/^`{$model->alias}`\.`(.*)`$/", $field, $match) > 0
			) {
				if (!$fieldFound) {
					$fieldFound = true;
					$firstPos = $key;
				}
				if ($match[1] === $model->primaryKey) {
					$primaryKeyFound = true;
					break;
				}
				if ($match[1] === '*') {
					$allFields = true;
					$allFieldsPos = $key;
					break;
				}
			}
		}

		if ($primaryKeyFound) {
			return $query;
		}
		if ($fieldFound && !$allFields) {
			array_splice($query['fields'], $firstPos, 0, $db->fields($model, null, "{$model->alias}.{$model->primaryKey}"));
			return $query;
		}
		if ($allFields) {
			unset($query['fields'][$allFieldsPos]);
			array_splice($query['fields'], $allFieldsPos, 0, $db->fields($model));
			return $query;
		}

		$query['fields'] = array_merge($query['fields'], $db->fields($model));

		return $query;
	}

	/**
	 * @param Model $model
	 * @param array $related
	 * @return array|null
	 */
	protected function _getRelationMap(Model $model, $related = array()) {
		$map = array();
		if (empty($related)) {
			$map[$model->alias] = array();
		}

		if (!isset(self::$_model[$model->alias])) {
			self::$_model[$model->alias] = &$model;
		}

		foreach ($related as $name => $children) {
			if (is_numeric($name)) {
				$name = $children;
				$children = array();
			}

			$query = array();
			foreach ($children as $key => $c) {
				if (!is_numeric($key) && in_array($key, $this->_queryParams)) {
					$query[$key] = $c;
					unset($children[$key]);
				}
			}

			$type = false;
			foreach ($this->_assocTypes as $assocType) {
				if (
					isset($model->{$assocType}) &&
					!empty($model->{$assocType}) &&
					isset($model->{$assocType}[$name]) &&
					isset($model->{$name}) &&
					is_object($model->{$name})
				) {
					$type = $assocType;
					if (!isset(self::$_model[$name])) {
						self::$_model[$name] = &$model->{$name};
					}
					break;
				}
			}

			if ($type === false) {
				trigger_error(__d('cake_dev', 'Model "%s" is not associated with model "%s"', $model->alias, $name), E_USER_WARNING);
				return null;
			}

			if (!isset($map[$model->alias][$type])) {
				$map[$model->alias][$type] = array();
			}

			$assoc = array(
				'name' => $name,
				'query' => $query
			);
			$map[$model->alias][$type][] = $assoc;

			if (!empty($children)) {
				$map = Hash::merge($map, $this->_getRelationMap($model->{$name}, $children));
			}
		}

		return $map;
	}

	/**
	 * @param Model $model
	 * @param Model $linkModel
	 * @param string $type
	 * @param array $options
	 * @return array
	 * @TODO: implement this method for supplemental 'fields' support
	 */
	protected function _addRequiredFields(Model $model, Model $linkModel, $type, $options) {
		if (!isset($options['fields'])) {
			return $options;
		}
		if (empty($options['fields'])) {
			unset($options['fields']);
			return $options;
		}

		switch ($type) {
			case 'hasOne':
			case 'belongsTo':
				$found = false;
				foreach ($options['fields'] as $field) {
					if ($field === "{$linkModel->alias}.*") {
						$found = true;
						break;
					}
					if ($field === "{$linkModel->alias}.{$linkModel->primaryKey}") {
						$found = true;
						break;
					}
				}
				if (!$found) {
					array_unshift($options['fields'],
						"{$linkModel->alias}.{$linkModel->primaryKey}"
					);
				}
				break;

			case 'hasMany':
				$found = false;
				foreach ($options['fields'] as $field) {
					if ($field === "{$linkModel->alias}.*") {
						$found = true;
						break;
					}
					if ($field === "{$linkModel->alias}.{$model->{$type}[$linkModel->alias]['foreignKey']}") {
						$found = true;
						break;
					}
				}
				if (!$found) {

					array_unshift($options['fields'],
						"{$linkModel->alias}.{$linkModel->primaryKey}",
						"{$linkModel->alias}.{$model->{$type}[$linkModel->alias]['foreignKey']}"
					);
				}
				break;
		}

		$options['fields'] = array_unique($options['fields']);

		return $options;
	}

	/**
	 * @param array $a
	 * @param array $b
	 * @return array
	 */
	protected function _mergeQueryParams($a, $b) {
		if (isset($b['fields']) && $b['fields'] !== null && !empty($b['fields'])) {
			if (is_string($b['fields']) && preg_match("/^COUNT/", $b['fields'])) {
				$a['fields'] = $b['fields'];
			} else {
				if (!is_array($b['fields'])) {
					$b['fields'] = (array) $b['fields'];
				}
				if (!isset($a['fields'])) {
					$a['fields'] = array();
				}
				if (!is_array($a['fields'])) {
					$a['fields'] = (array) $a['fields'];
				}
				$a['fields'] = array_unique(array_merge($a['fields'], $b['fields']));
			}
		}
		if (isset($b['limit']) && $b['limit'] !== null) {
			$a['limit'] = $b['limit'];
		}
		if (isset($b['offset']) && $b['offset'] !== null) {
			$a['offset'] = $b['offset'];
		}
		if (isset($b['order']) && $b['order'] !== null && !empty($b['order'])) {
			foreach ($b['order'] as $o) {
				if ($o !== null) {
					if (!isset($a['order'])) {
						$a['order'] = array();
					}
					if (!is_array($a['order']) && $a['order'] !== null) {
						$a['order'] = (array) $a['order'];
					}
					$a['order'][] = $o;
				}
			}
		}
		if (isset($b['group']) && $b['group'] !== null) {
			$a['group'] = $b['group'];
		}
		if (isset($b['conditions']) && $b['conditions'] !== null) {
			if (!is_array($b['conditions'])) {
				$b['conditions'] = (array) $b['conditions'];
			}
			if (!isset($a['conditions'])) {
				$a['conditions'] = array();
			}
			if (!is_array($a['conditions'])) {
				$a['conditions'] = (array) $a['conditions'];
			}
			$a['conditions'] = array_merge($a['conditions'], $b['conditions']);
		}

		return $a;
	}
}
