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
 * @subpackage    Wasabi.Plugin.Cms.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('FrontendAppController', 'Core.Controller');
App::uses('CmsLayoutManager', 'Cms.Lib');
App::uses('Route', 'Core.Model');

/**
 * @property CmsPage $CmsPage
 * @property Route $Route
 * @property array $data
 */

class CmsPagesFrontendController extends FrontendAppController {

	public $uses = array(
		'Cms.CmsPage'
	);

	/**
	 * View action
	 * Render a page for the frontend.
	 *
	 * @param integer $id The page id.
	 * @param integer $langId The language id.
	 * @throws NotFoundException
	 */
	public function view($id = null, $langId = null, $preview = null) {
		if ($id === null || $langId === null) {
			throw new NotFoundException();
		}

		App::uses('BackendAppController', 'Core.Controller');
		BackendAppController::loadLanguages($langId);

		$page = $this->CmsPage->find('first', array(
			'conditions' => array(
				'CmsPage.id' => $id
			)
		));
		if (!$page) {
			throw new NotFoundException();
		}
		$layout = CmsLayoutManager::getLayout($page['CmsPage']['cms_layout']);
		$layoutAttributes = $this->CmsPage->CmsPageLayoutAttribute->findForPage($page['CmsPage']['cms_layout'], $id);

		$this->prepareMeta($layoutAttributes, $page);

		$this->set(array(
			'lang' => Configure::read('Wasabi.content_language.lang'),
			'page' => $page,
			'layoutAttributes' => $layoutAttributes
		));

		$this->viewClass = 'Cms.CmsPage';
		$this->layoutPath = $layout->getLayoutPath();
		$this->layout = $layout->getId();

		$this->helpers['Meta'] = array(
			'className' => 'Core.Meta'
		);
		$this->helpers['Menu'] = array(
			'className' => 'Core.Menu'
		);
	}

	public static function prepareMeta(&$attributes, $page) {
		if (isset($attributes['og:title']) && $attributes['og:title'] === '' && $page['CmsPage']['page_title'] !== '') {
			$attributes['og:title'] = $page['CmsPage']['page_title'];
		}
		if (isset($attributes['og:description']) && $attributes['og:description'] === '' && $page['CmsPage']['meta_description'] !== '') {
			$attributes['og:description'] = $page['CmsPage']['meta_description'];
		}
		$attributes['viewport'] = 'width=device-width,initial-scale=1';
//		$attributes['twitter:card'] = 'summary';
	}
}
