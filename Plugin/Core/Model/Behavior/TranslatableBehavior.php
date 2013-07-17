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

App::uses('CakeException', 'Error');
App::uses('Hash', 'Utility');
App::uses('ModelBehavior', 'Model');
App::uses('Translation', 'Core.Model');

class TranslatableBehavior extends ModelBehavior {

	/**
	 * Holds the behavior settings for each model.
	 *
	 * @var array
	 */
	protected $_settings = array();

	/**
	 * Setup the behavior with supplied settings
	 *
	 * fields: (array) the model fields that should be translated
	 * translation_model: (string) the model class used to save and find translations
	 *
	 * @param Model $model
	 * @param array $settings
	 * @return void
	 */
	public function setup(Model $model, $settings = array()) {
		$defaults = array(
			'fields' => array(),
			'translation_model' => 'Core.Translation'
		);
		$this->_settings[$model->alias] = Hash::merge($defaults, $settings);
	}

	/**
	 * beforeFind callback
	 * runs before model find calls
	 * and modifies the query for translations
	 *
	 * @param Model $model Model using the behavior
	 * @param array $query the find query
	 * @return array the modified query
	 */
	public function beforeFind(Model $model, $query) {
		if ($this->_getContentLanguage() === null) {
			return $query;
		}

		/** @var DboSource $db */
		$db = $model->getDataSource();

		if (is_string($query['fields']) && "COUNT(*) AS {$db->name('count')}" == $query['fields']) {
			$query['fields'] = "COUNT(DISTINCT({$db->name($model->escapeField())})) {$db->alias}count";
			$query = $this->_prepareTranslationConditions($model, $query);
			$this->_settings[$model->alias]['beforeFind'] = 'COUNT';
			return $query;
		}

		$addFields = $this->_addFields($model, $query['fields']);

		if (isset($query['joins']) && !empty($query['joins'])) {
			foreach ($query['joins'] as $join) {
				if ($join['type'] !== 'LEFT') {
					continue;
				}
				$linkModelAlias = $join['alias'];
				if (isset($model->belongsTo[$linkModelAlias])) {
					$linkModel = $model->{$linkModelAlias};
					$isTranslatable =
						isset($linkModel->actsAs) &&
						!empty($linkModel->actsAs) &&
						isset($linkModel->actsAs['Core.Translatable']);
					if (!$isTranslatable) {
						continue;
					}
					$this->setup($linkModel, $linkModel->actsAs['Core.Translatable']);
					$addFields = $this->_addFields($linkModel, $query['fields'], $addFields);
				}
			}
		}

		foreach ($addFields as $field) {
			if ($model->alias !== $field['model']->alias) {
				$query = $this->_addJoinForTranslatedField($field['model'], $query, $field['field'], null, $model);
			} else {
				$query = $this->_addJoinForTranslatedField($field['model'], $query, $field['field']);
			}

		}

		return $query;
	}

	public function afterFind(Model $model, $results, $primary) {
		if ($this->_getContentLanguage() === null) {
			return $results;
		}

		$isCountQuery = isset($this->_settings[$model->alias]['beforeFind']) && $this->_settings[$model->alias]['beforeFind'] === 'COUNT';

		if (empty($results) || $isCountQuery) {
			return $results;
		}

		foreach ($results as &$row) {
			$modelKeys = array();
			foreach ($row as $key => $translations) {
				if (!is_numeric($key)) {
					$modelKeys[] = $key;
					continue;
				}
				foreach ($translations as $identifier => $translation) {
					foreach ($modelKeys as $mKey) {
						$m = array();
						if (preg_match("/i18n[_]{1,2}{$mKey}_(\S+)/", $identifier, $m) === 1) {
							if ($translation !== null) {
								$row[$mKey][$m[1]] = $translation;
							}
							unset($row[$key][$identifier]);
						}
					}
				}
				if (empty($row[$key])) {
					unset($row[$key]);
				}
			}
		}

		return $results;
	}

	public function beforeSave(Model $model, $options = array()) {
		parent::beforeSave($model);

		$languages = $this->_getFrontendLanguages();
		if ($languages === null) {
			return true;
		}

		// on update unset translation data fields if the current language is not the default language
		// and reinsert those in afterSave()
		// this way only the default language data will be stored in the original model table
		if ($this->_getContentLanguage('id') !== $languages[0]['id'] && $model->id !== false) {
			$this->_settings[$model->alias]['data'] = array();

			foreach ($this->_settings[$model->alias]['fields'] as $field) {
				if (isset($model->data[$model->alias][$field])) {
					$this->_settings[$model->alias]['data'][$field] = $model->data[$model->alias][$field];
					unset($model->data[$model->alias][$field]);
				}
			}
		}

		return true;
	}

	public function afterSave(Model $model, $created) {
		parent::afterSave($model, $created);

		$languages = $this->_getFrontendLanguages();
		if ($languages === null) {
			return;
		}

		$translationModel = $this->_getTranslationModel($model);

		if ($created) {

			// create all translation fields for the new model record
			foreach ($this->_settings[$model->alias]['fields'] as $field) {
				$data = array(
					'plugin' => $model->plugin,
					'model' => $model->alias,
					'foreign_key' => $model->id,
					'field' => $field,
					'content' => $model->data[$model->alias][$field]
				);

				// for all available and in progress frontend languages
				foreach ($languages as $language) {
					$data['language_id'] = $language['id'];
					$translationModel->create();
					$translationModel->save($data);
				}
			}
		} else {
			if (isset($this->_settings[$model->alias]['data']) && !empty($this->_settings[$model->alias]['data'])) {
				foreach ($this->_settings[$model->alias]['fields'] as $field) {
					if (isset($this->_settings[$model->alias]['data'][$field])) {
						$model->data[$model->alias][$field] = $this->_settings[$model->alias]['data'][$field];
					}
				}
				unset($this->_settings[$model->alias]['data']);
			}

			$existingTranslations = $translationModel->find('list', array(
				'fields' => array($translationModel->alias . '.field', $translationModel->alias . '.id'),
				'conditions' => array(
					'plugin' => $model->plugin,
					'model' => $model->alias,
					'foreign_key' => $model->id,
					'language_id' => $this->_getContentLanguage('id')
				)
			));

			foreach ($this->_settings[$model->alias]['fields'] as $field) {
				if (!isset($existingTranslations[$field])) {
					$data = array(
						'plugin' => $model->plugin,
						'model' => $model->alias,
						'foreign_key' => $model->id,
						'field' => $field,
						'language_id' => $this->_getContentLanguage('id'),
						'content' => $model->data[$model->alias][$field]
					);
				} else {
					$data = array(
						'id' => $existingTranslations[$field],
						'content' => $model->data[$model->alias][$field]
					);
				}
				$translationModel->create();
				$translationModel->save($data);
			}
		}
	}

	/**
	 * @param Model $model
	 * @param array $query
	 * @return array
	 */
	protected function _prepareTranslationConditions(Model $model, $query) {
		if (empty($query['conditions']) || !is_array($query['conditions'])) {
			return $query;
		}

		foreach ($query['conditions'] as $col => $value) {
			foreach ($this->_settings[$model->alias]['fields'] as $field) {
				$colParts = is_numeric($col) ? preg_split("/\./", $value) : preg_split("/\./", $col);

				if (!is_numeric($col) && $col == $field) {
					unset($query['conditions'][$col]);
					$query = $this->_addJoinForTranslatedField($model, $query, $field, $value);
					break;
				}

				if (count($colParts) >= 1 &&
					$colParts[0] == $model->alias &&
					$colParts[1] == $field
				) {
					unset($query['conditions'][$col]);
					$query = $this->_addJoinForTranslatedField($model, $query, $field, $value);
				}
			}
		}

		return $query;
	}

	/**
	 * @param Model $model
	 * @param array $query
	 * @param string $field
	 * @param boolean|null|string $value
	 * @param Model|null $sourceModel
	 * @return mixed
	 */
	protected function _addJoinForTranslatedField(Model $model, $query, $field, $value = null, $sourceModel = null) {
		$translationModel = $this->_getTranslationModel($model);
		$joinTable = $this->_getJoinTable($model);

		/** @var DboSource $db */
		$db = $model->getDataSource();

		$alias = "{$model->alias}__{$translationModel->alias}__{$field}";
		if ($db->config['datasource'] === 'Database/Mysql') {
			$aliasVirtual = "i18n__{$model->alias}_{$field}";
		} else {
			$aliasVirtual = "i18n_{$model->alias}_{$field}";
		}

		if (!isset($query['fields']) || $query['fields'] === null) {
			$query['fields'] = $db->fields($model);
		}

		if (is_string($query['fields']) && strpos($query['fields'], 'COUNT(') === false) {
			$query['fields'] = (array) $query['fields'];
		}
		if (is_array($query['fields'])) {
			$trField = "({$db->name($alias . '.content')}) AS {$db->name($aliasVirtual)}";
			$query['fields'][] = $trField;
		}

		$query['joins'][] = array(
			'type' => 'LEFT',
			'alias' => $alias,
			'table' => $joinTable,
			'conditions' => array(
				"{$model->alias}.{$model->primaryKey}" => $db->identifier("{$alias}.foreign_key"),
				"{$alias}.plugin" => $model->plugin,
				"{$alias}.model" => $model->name,
				"{$alias}.language_id" => $this->_getContentLanguage('id'),
				"{$alias}.field" => $field,
			)
		);

		if ($value !== null) {
			$query['conditions']["{$alias}.content"] = $value;
		}

		return $query;
	}

	/**
	 * Get the translation model object.
	 * Initialize the translation model object on first call.
	 *
	 * @param Model $model
	 * @return Model
	 */
	protected function _getTranslationModel(Model $model) {
		$tm = $this->_settings[$model->alias]['translation_model'];
		if (!isset($this->_settings['translation_models'][$tm])) {
			$translationModel = ClassRegistry::init($tm);
			$this->_settings['translation_models'][$tm] = $translationModel;
		}
		return $this->_settings['translation_models'][$tm];
	}

	/**
	 * @param Model $model
	 * @return mixed
	 */
	protected function _getJoinTable(Model $model) {
		if (!isset($this->_settings[$model->alias]['joinTable'])) {
			$translationModel = $this->_getTranslationModel($model);

			if (!empty($translationModel->tablePrefix)) {
				$tablePrefix = $translationModel->tablePrefix;
			} else {
				$db = $model->getDataSource();
				$tablePrefix = $db->config['prefix'];
			}

			$joinTable = new stdClass();
			$joinTable->tablePrefix = $tablePrefix;
			$joinTable->table = $translationModel->table;
			$joinTable->schemaName = $translationModel->getDataSource()->getSchemaName();

			$this->_settings[$model->alias]['joinTable'] = $joinTable;
		}
		return $this->_settings[$model->alias]['joinTable'];
	}

	/**
	 * @return mixed|null
	 */
	protected function _getFrontendLanguages() {
		$languages = Configure::read('Languages.frontend');

		if (empty($languages)) {
			$languages = null;
		}

		return $languages;
	}

	/**
	 * @param null|string $field
	 * @return mixed|null
	 */
	protected function _getContentLanguage($field = null) {
		if ($field === null) {
			return Configure::read('Wasabi.content_language');
		}

		return Configure::read('Wasabi.content_language.' . $field);
	}

	protected function _getDefaultLanguage($field = null) {
		$languages = $this->_getFrontendLanguages();

		$language = null;
		if ($languages) {
			$language = $languages[0];
		}

		if ($field !== null && isset($language[$field])) {
			return $language[$field];
		}

		return $language;
	}

	protected function _addFields(Model $model, $fields = null, $addFields = array()) {
		if (!is_array($fields)) {
			$fields = (array) $fields;
		}
		if (empty($fields)) {
			foreach ($this->_settings[$model->alias]['fields'] as $field) {
				$addFields[] = array(
					'model' => $model,
					'field' => $field
				);
			}
		} else {
			$isAllFields = (
				in_array("{$model->alias}.*", $fields) ||
				in_array($model->escapeField('*'), $fields)
			);
			/** @var $db DboSource */
			$db = $model->getDataSource();
			foreach ($this->_settings[$model->alias]['fields'] as $field) {
				$f1 = preg_grep("/^{$db->name($model->alias . '.' . $field)}/", $fields);
				if (
					$isAllFields ||
					!empty($f1) ||
					in_array("{$model->alias}.{$field}", $fields) ||
					in_array($field, $fields) ||
					array_search($db->name("{$model->alias}.{$field}"), $fields)
				) {
					$addFields[] = array(
						'model' => $model,
						'field' => $field
					);
				}
			}
		}
		return $addFields;
	}

}
