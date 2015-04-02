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

class CmsPageLayoutAttribute extends CoreAppModel {

	/**
	 * Attached Behaviors
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Core.Translatable' => array(
			'fields' => array(
				'content'
			)
		)
	);

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'CmsPage' => array(
			'className' => 'Cms.CmsPage'
		)
	);

	/**
	 * Custom find method to get all translated attributes
	 * for a specifc layout and page. (ready to be used as request data
	 * in forms).
	 *
	 * @param integer $layoutId
	 * @param integer $pageId
	 * @return array
	 */
	public function findAll($layoutId, $pageId = null) {
		$cmsPageLayoutAttributes = array();

		$layout = CmsLayoutManager::getLayout($layoutId);
		$attributes = $layout->getAttributes();
		unset($layout);

		if (empty($attributes)) {
			return $cmsPageLayoutAttributes;
		}

		$pageAttributes = array();
		if ($pageId !== null) {
			$pageAttributes = $this->find('all', array(
				'conditions' => array(
					'cms_page_id' => $pageId,
					'cms_layout' => $layoutId,
					'cms_layout_attribute' => array_keys($attributes)
				)
			));
		}

		foreach ($attributes as $cmsLayoutAttribute => &$attr) {
			$data = $attr;
			$data['cms_layout'] = $layoutId;
			$data['cms_layout_attribute'] = $cmsLayoutAttribute;
			$data['content'] = '';
			foreach ($pageAttributes as $pAttr) {
				if ($cmsLayoutAttribute === $pAttr['CmsPageLayoutAttribute']['cms_layout_attribute']) {
					$data['id'] = $pAttr['CmsPageLayoutAttribute']['id'];
					$data['content'] = $pAttr['CmsPageLayoutAttribute']['content'];
					break;
				}
			}
			$cmsPageLayoutAttributes[] = $data;
		}

		return $cmsPageLayoutAttributes;
	}

	public function findForPage($layoutId, $pageId) {
		$attributes = $this->find('list', array(
			'fields' => array('cms_layout_attribute', 'content'),
			'conditions' => array(
				'cms_page_id' => $pageId,
				'cms_layout' => $layoutId,
				'cms_layout_attribute' => array_keys(CmsLayoutManager::getLayout($layoutId)->getAttributes())
			)
		));

		return $attributes;
	}

}
