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
 * @method EnhancedTreeBehavior generateTreeList(mixed $conditions, string $keyPath, string $valuePath, string $spacer, integer $recursive, integer $maxDepth)
 * @method findById(integer $id)
 */
class MenuItem extends CoreAppModel {

	const TYPE_EXTERNAL_LINK = 'ExternalLink';
	const TYPE_OBJECT = 'Object';
	const TYPE_ACTION = 'Action';
	const TYPE_CUSTOM_ACTION = 'CustomAction';

	public $actsAs = array(
		'Core.EnhancedTree',
		'Core.Translatable' => array(
			'fields' => array(
				'name'
			)
		)
	);

	public $order = 'MenuItem.lft ASC';

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Menu' => array(
			'className' => 'Core.Menu',
			'counterCache' => true
		)
	);

	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a name for the menu item.'
			)
		),
		'external_link' => array(
			'isValid' => array(
				'rule' => 'url',
				'message' => 'Please enter a valid external link.'
			)
		),
		'controller' => array(
			'isValid' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a valid controller name.'
			)
		),
		'action' => array(
			'isValid' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a valid action name.'
			)
		)
	);

	public $findMethods = array('publishedThreaded' => true);

	/**
	 * Public accessor
	 *
	 * @return self
	 */
	public static function instance() {
		static $instance;
		if (!$instance) {
			$instance = ClassRegistry::init('Core.MenuItem');
		}
		return $instance;
	}

	/**
	 * Find all menu items with find $options
	 *
	 * @param array $options
	 * @return array
	 */
	public function findAll($options = array()) {
		return $this->find('all', $options);
	}

	public function validateExternalLink($content) {
		var_dump($content);
		die();
	}

	protected function _findPublishedThreaded($state, $query, $results = array()) {
		if ($state === 'before') {
			if (!isset($query['menu'])) {
				user_error(__d('core', 'You have to add a find option: "menu" => menu_id'));
			}
			$conditions = array($this->alias . '.menu_id' => $query['menu']);
			unset($query['menu']);
			$menuItems = $this->find('all', array(
				'conditions' => $conditions
			));
			$referencedObjects = array_unique(Hash::extract($menuItems, '{n}.' . $this->alias . '.foreign_model'));
			$related = array();
			$fields = array();
			$conditions['or'] = array(
				array('not' => array($this->alias . '.external_link' => null)),
				array('and' => array(
					$this->alias . '.external_link' => null,
					$this->alias . '.foreign_model' => null
				))
			);
			foreach ($referencedObjects as $obj) {
				if ($obj === null) {
					continue;
				}
				list(, $model) = pluginSplit($obj);
				$this->bindModel(array(
					'belongsTo' => array(
						$model => array(
							'className' => $obj,
							'foreignKey' => 'foreign_id'
						)
					)
				));
				$related[] = $model;
				$fields += array($model.'.id', $model.'.status');
				$conditions['or'][] = array($model.'.status' => 'hidden');
			}

			$query['fields'] = array('MenuItem.*') + $fields;
			$query['conditions'] = $conditions;
			$query['related'] = $related;

			if ($this->findMethods['threaded'] === true) {
				$query = $this->_findThreaded('before', $query);
			}

			return $query;
		}

		if ($this->findMethods['threaded'] === true) {
			return $this->_findThreaded('after', $query, $results);
		}
		return $results;
	}

}
