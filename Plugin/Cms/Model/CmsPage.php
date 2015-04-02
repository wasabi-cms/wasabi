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
 * @subpackage    Wasabi.Plugin.Cms.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CoreAppModel', 'Core.Model');
App::uses('Route', 'Core.Model');
App::uses('Collections', 'Core.Lib');
App::uses('CollectionItems', 'Core.Lib');

/**
 * @property CmsPageLayoutAttribute $CmsPageLayoutAttribute
 */
class CmsPage extends CoreAppModel {

	const STATUS_PUBLISHED  = 'published';
	const STATUS_HIDDEN = 'hidden';

	/**
	 * Attached Behaviors
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Tree',
		'Core.Sluggable',
		'Core.Translatable' => array(
			'fields' => array(
				'name',
				'slug',
				'page_title',
				'meta_description',
				'status',
				'cached'
			)
		)
	);

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
//		'Collection' => array(
//			'className' => 'Core.Collection'
//		),
//		'CollectionItem' => array(
//			'className' => 'Core.CollectionItem'
//		)
//		'CmsLayout' => array(
//			'className' => 'Cms.CmsLayout'
//		)
	);

	public $hasOne = array(
		'Collection' => array(
			'className' => 'Core.Collection',
			'foreignKey' => 'page_id'
		)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'CmsPageLayoutAttribute' => array(
			'className' => 'Cms.CmsPageLayoutAttribute',
			'dependent' => true
		)
	);

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a name for this page.'
			)
		)
	);

	/**
	 * beforeSave callback
	 *
	 * @param array $options
	 * @return boolean
	 */
	public function beforeSave($options = array()) {
		// ensure parent_id and status are set on page creation
		if (!isset($this->data['CmsPage']['id'])) {
			if (!isset($this->data['CmsPage']['parent_id']) || $this->data['CmsPage']['parent_id'] === '') {
				$this->data['CmsPage']['parent_id'] = 0;
			}
			if (!isset($this->data['CmsPage']['status'])) {
				$this->data['CmsPage']['status'] = self::STATUS_HIDDEN;
			}
		}
		// empty collection and/or collection_item depending on the selected page type
		if (isset($this->data['CmsPage']['page_type'])) {
			if ($this->data['CmsPage']['page_type'] === 'normal') {
				$this->data['CmsPage']['collection'] = '';
				$this->data['CmsPage']['collection_item'] = '';
			} elseif ($this->data['CmsPage']['page_type'] === 'collection') {
				$this->data['CmsPage']['collection_item'] = '';
			} elseif ($this->data['CmsPage']['page_type'] === 'item') {
				$this->data['CmsPage']['collection'] = '';
			}
		}
		return parent::beforeSave($options);
	}

	/**
	 * afterSave callback
	 *
	 * @param boolean $created
	 */
	public function afterSave($created) {
		if ($created) {
			$path = $this->getPath($this->id);
			foreach (Configure::read('Languages.frontend') as $lang) {
				$defaultUrl = '';
				if ($lang['id'] !== Configure::read('Languages.frontend.0.id')) {
					$defaultUrl .= '/' . $lang['locale'];
				}
				foreach ($path as $p) {
					$defaultUrl .= '/' . $p[$this->alias]['slug'];
				}
				$route = ClassRegistry::init('Core.Route');
				$route->create();
				$route->save(array(
					'plugin' => 'cms',
					'controller' => 'cms_pages_frontend',
					'action' => 'view',
					'params' => $this->id . '|' . $lang['id'],
					'url' => $defaultUrl
				));
			}
		}

		if (!$created) {
			// if the page holds a collection, ensure that no other page is marked to hold this collection.
//			if ($this->data['CmsPage']['page_type'] === 'collection' && $this->data['CmsPage']['collection'] !== '') {
//				/** @var DboSource $db */
//				$db = $this->getDataSource();
//				$this->updateAll(array(
//					'CmsPage.page_type' => $db->value('normal', 'string'),
//					'CmsPage.collection' => null
//				), array(
//					'CmsPage.page_type' => 'collection',
//					'CmsPage.collection' => $this->data['CmsPage']['collection'],
//					'CmsPage.id <>' => $this->id
//				));
//			}
//			if ($this->data['CmsPage']['page_type'] === 'item' && $this->data['CmsPage']['collection_item'] !== '') {
//				/** @var DboSource $db */
//				$db = $this->getDataSource();
//				$this->updateAll(array(
//					'CmsPage.page_type' => $db->value('normal', 'string'),
//					'CmsPage.collection_item' => null
//				), array(
//					'CmsPage.page_type' => 'item',
//					'CmsPage.collection_item' => $this->data['CmsPage']['collection_item'],
//					'CmsPage.id <>' => $this->id
//				));
//			}
			// if the page hold  collection items, ensure that no other page is holding those items.
		}
		// clearCache
	}

	/**
	 * Custom find method to get all pages and their
	 * layout name.
	 *
	 * @return array
	 */
	public function findForIndex() {
		return $this->find('threaded', array(
			'order' => 'CmsPage.lft ASC',
			'related' => array(
				'Collection'
			)
		));
	}

	/**
	 * Custom find method to get a single page
	 * with its layout name and corresponding
	 * content areas.
	 *
	 * @param $id
	 * @return array
	 */
	public function findForEdit($id) {
		return $this->find('first', array(
			'conditions' => array(
				'CmsPage.id' => $id
			),
//			'contain' => array(
//				'CmsLayout' => array(
//					'CmsContentArea'
//				)
//			)
		));
	}

	/**
	 * Public accessor
	 *
	 * @return self
	 */
	public static function instance() {
		static $instance;
		if (!$instance) {
			$instance = ClassRegistry::init('Cms.CmsPage');
		}
		return $instance;
	}

	public static function isActive($menuItem, $params) {
		if ($params['plugin'] === 'cms' && (
			($params['controller'] === 'cms_pages' && $params['action'] === 'live') ||
			($params['controller'] === 'cms_pages_frontend' && $params['action'] === 'view')
		) && isset($params['pass']) && isset($params['pass'][0])) {
			return ($menuItem['foreign_id'] === $params['pass'][0]);
		}

		return false;
	}

	public static function getCollection($page) {
		if (!isset($page['Collection']['id']) || !Collections::instance()->exists($page['Collection']['identifier'])) {
			return false;
		}
		return Collections::instance()->getDisplayName($page['Collection']['identifier']);
	}

	public static function getCollectionItem($page) {
		if (!isset($page['Collection']['id']) || $page['Collection']['type'] !== 'item' || !CollectionItems::instance()->exists($page['Collection']['identifier'])) {
			return false;
		}
		return CollectionItems::instance()->getDisplayName($page['Collection']['identifier']);
	}

}
