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

App::uses('CmsBackendAppController', 'Cms.Controller');
App::uses('CmsLayoutManager', 'Cms.Lib');
App::uses('Collections', 'Core.Lib');
App::uses('CollectionItems', 'Core.Lib');
App::uses('Hash', 'Utility');
App::uses('Route', 'Core.Model');

/**
 * @property CmsPage $CmsPage
 * @property Route $Route
 * @property array $data
 */

class CmsPagesController extends CmsBackendAppController {

	/**
	 * Index action
	 * GET
	 *
	 * @return void
	 */
	public function index() {
		$this->set(array(
			'pages' => $this->CmsPage->findForIndex(),
			'closedPages' => isset($_COOKIE['closed_pages']) ? explode(',', $_COOKIE['closed_pages']) : array(),
			'title_for_layout' => __d('cms', 'CMS Pages')
		));
	}

	/**
	 * Add action
	 * GET | POST
	 *
	 * @param integer $parentId
	 * @return void
	 */
	public function add($parentId = null) {
		if ($this->request->is('post') && !empty($this->data)) {
			if ($parentId !== null) {
				if ($this->CmsPage->exists($parentId)) {
					$this->request->data['CmsPage']['parent_id'] = $parentId;
				} else {
					$this->Session->setFlash(__d('cms', 'No Page with id <strong>%s</strong> exists.', array($parentId)), 'default', array('class' => 'error'));
					$this->redirect(array('action' => 'index'));
					return;
				}
			}
			if ($this->CmsPage->saveAll($this->data)) {
				$this->Session->setFlash(__d('cms', 'The page <strong>%s</strong> has been added.', array($this->data['CmsPage']['name'])), 'default', array('class' => 'success'));
				if (isset($this->data['btn_update'])) {
					$id = $this->CmsPage->getLastInsertID();
					$this->redirect(array('action' => 'edit', $id));
					return;
				}
				$this->redirect(array('action' => 'index'));
				return;
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		} else {
			if ($parentId !== null && !$this->CmsPage->exists($parentId)) {
				$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
				$this->redirect(array('action' => 'index'));
				return;
			}
		}

		$layouts = CmsLayoutManager::getLayoutsForSelect();
		reset($layouts);
		$layoutAttributes = $this->CmsPage->CmsPageLayoutAttribute->findAll(key($layouts));
		if (empty($this->data)) {
			$this->request->data['CmsPageLayoutAttribute'] = $layoutAttributes;
		} elseif (isset($this->request->data['CmsPageLayoutAttribute'])) {
			$this->request->data['CmsPageLayoutAttribute'] = Hash::merge($layoutAttributes, $this->request->data['CmsPageLayoutAttribute']);
		}

		$this->set(array(
			'layouts' => $layouts,
			'title_for_layout' => __d('cms', 'Add a new Page')
		));
	}

	/**
	 * Edit action
	 * GET | PUT
	 *
	 * @param integer $id
	 * @return void
	 */
	public function edit($id = null) {
		if ($id === null || !$this->CmsPage->exists($id)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
			return;
		}
		if (!$this->request->is('put') && empty($this->data)) {
			$this->request->data = $this->CmsPage->findForEdit($id);
		} else {
			if ($this->CmsPage->saveAll($this->data)) {
				$this->Session->setFlash(__d('cms', 'The page <strong>%s</strong> has been updated successfully.', array($this->data['CmsPage']['name'])), 'default', array('class' => 'success'));
				if (isset($this->data['btn_update'])) {
					$this->redirect(array('action' => 'edit', $id));
					return;
				}
				$this->redirect(array('action' => 'index'));
				return;
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		}

		$layoutAttributes = $this->CmsPage->CmsPageLayoutAttribute->findAll($this->request->data['CmsPage']['cms_layout'], $id);
		if (!$this->request->is('put')) {
			$this->request->data['CmsPageLayoutAttribute'] = $layoutAttributes;
		} elseif (isset($this->request->data['CmsPageLayoutAttribute'])) {
			$this->request->data['CmsPageLayoutAttribute'] = Hash::merge($layoutAttributes, $this->request->data['CmsPageLayoutAttribute']);
		}

		$this->Route = ClassRegistry::init('Core.Route');
		$this->set(array(
			'layouts' => CmsLayoutManager::getLayoutsForSelect(),
			'routes' => $this->Route->find('all', array(
				'conditions' => array(
					'page_id' => $id,
					'language_id' => Configure::read('Wasabi.content_language.id')
				),
				'order' => 'Route.url ASC'
			)),
			'routeTypes' => $this->Route->getRouteTypes(),
			'title_for_layout' => __d('cms', 'Edit Page')
		));

		$this->render('add');
	}

	/**
	 * Delete action
	 * POST
	 *
	 * @param integer $id
	 */
	public function delete($id = null) {
		if (!$this->request->is('post') || $id === null || !$this->CmsPage->exists($id)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
			return;
		}
		$this->CmsPage->delete($id);

		$this->Route = ClassRegistry::init('Core.Route');
		$this->Route->deleteAll(array(
			'page_id' => $id
		));

		$this->Session->setFlash(__d('cms', 'The page has been deleted.'), 'default', array('class' => 'success'));
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * Editlive action
	 *
	 * @param integer $id
	 */
	public function live_edit($id = null) {
		if ($id === null || !$this->CmsPage->exists($id)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
			return;
		}

//		$this->helpers['Meta'] = array(
//			'className' => 'Core.Meta'
//		);

		$this->set(array(
			'id' => $id,
			'bodyCssClass' => array('live-edit', 'nav-closed')
		));

		$this->layout = 'live_edit';
	}

	/**
	 * Live action
	 * Render a page for the editlive iframe
	 *
	 * @param integer $id
	 * @throws CakeException
	 */
	public function live($id = null) {
		if ($id === null || !$this->CmsPage->exists($id)) {
			throw new CakeException($this->invalidRequestMessage);
		}

		$page = $this->CmsPage->findForEdit($id);
		$layout = CmsLayoutManager::getLayout($page['CmsPage']['cms_layout']);
		$layoutAttributes = $this->CmsPage->CmsPageLayoutAttribute->findForPage($page['CmsPage']['cms_layout'], $id);

		App::uses('CmsPagesFrontendController', 'Cms.Controller');
		CmsPagesFrontendController::prepareMeta($layoutAttributes, $page);

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

	/**
	 * Reorder action
	 * AJAX POST
	 *
	 * @return void
	 * @throws CakeException
	 */
	public function reorder() {
		if ($this->request->is('ajax') && $this->request->is('post')) {
			if (empty($this->data) || !isset($this->data['CmsPage']) || empty($this->data['CmsPage'])) {
				return;
			} else {
				$this->CmsPage->Behaviors->unload('Tree');
				$this->CmsPage->Behaviors->unload('Core.Sluggable');
				$this->CmsPage->Behaviors->unload('Core.Translatable');
				if ($this->CmsPage->saveAll($this->data['CmsPage'])) {
					$this->set('success', true);
					$this->set('_serialize', array('success'));
				}
			}
		} else {
			throw new CakeException($this->invalidRequestMessage, 400);
		}
	}

	/**
	 * Attributes action
	 * Renders the layout attributes element for $layoutId and $pageId
	 * AJAX GET
	 *
	 * @param integer $layoutId
	 * @param integer $pageId
	 * @return void
	 * @throws CakeException
	 */
	public function attributes($layoutId = null, $pageId = null) {
		if (!$this->request->is('ajax') ||
			!$this->request->is('get') ||
			$layoutId === null ||
			!CmsLayoutManager::layoutExists($layoutId) ||
			($pageId !== null && !$this->CmsPage->exists($pageId))
		) {
			throw new CakeException($this->invalidRequestMessage, 500);
		}

		$this->request->data['CmsPageLayoutAttribute'] = $this->CmsPage->CmsPageLayoutAttribute->findAll($layoutId, $pageId);

		$this->render('attributes');
	}
}
