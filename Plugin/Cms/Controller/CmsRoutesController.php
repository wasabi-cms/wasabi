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

/**
 * @property Route $Route
 * @property array $data
 */

class CmsRoutesController extends CmsBackendAppController {

	/**
	 * Models used by this controller
	 *
	 * @var array
	 */
	public $uses = array(
		'Core.Route'
	);

	/**
	 * Add action
	 * AJAX POST
	 *
	 * @return void
	 * @throws CakeException
	 */
	public function add() {
		if (!$this->request->is('ajax') ||
			!$this->request->is('post') ||
			empty($this->data) ||
			!isset($this->data['Route']['url']) ||
			!isset($this->data['Route']['type']) ||
			!isset($this->data['pageId'])
		) {
			throw new CakeException($this->invalidRequestMessage, 400);
		}

		if (substr($this->data['Route']['url'], 0, 1) !== '/') {
			$this->request->data['Route']['url'] = '/' . $this->data['Route']['url'];
		}
		$pageId = $this->data['pageId'];
		$data = array(
			'url' => $this->data['Route']['url'],
			Route::PAGE_KEY => $pageId,
			Route::LANG_KEY => Configure::read('Wasabi.content_language.id')
		);

		if (isset($this->data['Route']['foreign_id'])) {
			$data['foreign_id'] = $this->data['Route']['foreign_id'];
		}

		switch ($this->data['Route']['type']) {
			case Route::TYPE_REDIRECT_ROUTE:
				$currentDefaultRoute = array_merge($data, array(
					'redirect_to' => null
				));
				unset($currentDefaultRoute['url']);
				$currentDefaultRoute = $this->Route->find('first', array('conditions' => $currentDefaultRoute));
				if ($currentDefaultRoute) {
					$data['redirect_to'] = $currentDefaultRoute['Route']['id'];
					$data['status_code'] = 301;
					$this->Route->create();
					if ($this->Route->save($data)) {
						$this->Session->setFlash(__d('cms', 'New redirect url <strong>%s</strong> has been added.', array($data['url'])), 'default', array('class' => 'success'));
						unset($this->request->data);
					} else {
						$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
					}
				} else {
					$this->Session->setFlash('Please create a default route first.', 'default', array('class' => 'error'));
				}
				break;

			case Route::TYPE_DEFAULT_ROUTE:
				$this->Route->create();
				if ($this->Route->save($data)) {
					$otherRoutes = array_merge($data, array(
						'id <>' => $this->Route->getLastInsertID()
					));
					unset($otherRoutes['url']);
					$otherRoutes = $this->Route->find('all', array('conditions' => $otherRoutes));
					if ($otherRoutes) {
						$updateOtherRoutes = array();
						foreach ($otherRoutes as $r) {
							$updateOtherRoutes[] = array(
								'id' => $r['Route']['id'],
								'redirect_to' => $this->Route->getLastInsertID(),
								'status_code' => 301
							);
						}
						$this->Route->create();
						$this->Route->saveAll($updateOtherRoutes);
					}
					$this->Session->setFlash(__d('cms', 'New default url <strong>%s</strong> has been added.', array($data['url'])), 'default', array('class' => 'success'));
					unset($this->request->data);
				} else {
					$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
				}
				break;

			default:
				throw new CakeException($this->invalidRequestMessage, 400);
		}

		unset($data['url']);
		$this->set(array(
			'routes' => $this->Route->find('all', array(
				'conditions' => array(
					Route::PAGE_KEY => $pageId,
					Route::LANG_KEY => Configure::read('Wasabi.content_language.id')
				),
				'order' => 'Route.url ASC'
			)),
			'routeTypes' => $this->Route->getRouteTypes()
		));

		$this->render('add');
	}

	/**
	 * Make Default action
	 * AJAX POST
	 *
	 * @param integer $id
	 * @return void
	 * @throws CakeException
	 */
	public function make_default($id = null) {
		if (!$this->request->is('ajax') ||
			!$this->request->is('post') ||
			$id === null ||
			!$this->Route->exists($id)
		) {
			throw new CakeException($this->invalidRequestMessage, 400);
		}

		$route = $this->Route->findById($id);
		$routes = array();
		$routes[] = array(
			'id' => $id,
			'redirect_to' => null,
			'status_code' => null
		);
		$alternativeRoutes = $this->Route->find('all', array(
			'conditions' => array(
				'id <>' => $id,
				Route::PAGE_KEY => $route[$this->Route->alias][Route::PAGE_KEY],
				Route::LANG_KEY => $route[$this->Route->alias][Route::LANG_KEY]
			)
		));
		foreach ($alternativeRoutes as $aRoute) {
			$routes[] = array(
				'id' => $aRoute['Route']['id'],
				'redirect_to' => $id,
				'status_code' => 301
			);
		}
		$this->Route->create();
		$this->Route->saveAll($routes);
		$this->Session->setFlash(__d('cms', '<strong>%s</strong> is now the new Default Route for this Page.', array($route['Route']['url'])), 'default', array('class' => 'success'));

		$this->set(array(
			'routes' => $this->Route->find('all', array(
				'conditions' => array(
					Route::PAGE_KEY => $route[$this->Route->alias][Route::PAGE_KEY],
					Route::LANG_KEY => $route[$this->Route->alias][Route::LANG_KEY]
				),
				'order' => 'Route.url ASC'
			)),
			'routeTypes' => $this->Route->getRouteTypes()
		));

		$this->render('add');
	}

	/**
	 * Delete action
	 * AJAX POST
	 *
	 * @param integer $id
	 * @return void
	 * @throws CakeException
	 */
	public function delete($id = null) {
		if (!$this->request->is('ajax') ||
			!$this->request->is('post') ||
			$id === null ||
			!$this->Route->exists($id)
		) {
			throw new CakeException($this->invalidRequestMessage, 400);
		}

		$route = $this->Route->findById($id);
		$alternativeRoutes = $this->Route->find('all', array(
			'conditions' => array(
				'id <>' => $route['Route']['id'],
				Route::PAGE_KEY => $route[$this->Route->alias][Route::PAGE_KEY],
				Route::LANG_KEY => $route[$this->Route->alias][Route::LANG_KEY]
			),
			'order' => 'Route.url ASC'
		));
		if ($alternativeRoutes && count($alternativeRoutes) >= 1) {
			$this->Route->delete($id);
			$this->Session->setFlash(__d('cms', 'The Url <strong>%s</strong> has been deleted.', array($route['Route']['url'])), 'default', array('class' => 'success'));
			if ($route['Route']['redirect_to'] === null) {
				$newDefaultRoute = array(
					'id' => array_shift($alternativeRoutes)['Route']['id'],
					'redirect_to' => null,
					'status_code' => null
				);
				$this->Route->create();
				$this->Route->save($newDefaultRoute);
				$newRedirectRoutes = array();
				foreach ($alternativeRoutes as $aRoute) {
					$newRedirectRoutes[] = array(
						'id' => $aRoute['Route']['id'],
						'redirect_to' => $newDefaultRoute['id'],
						'status_code' => 301
					);
				}
				if (!empty($newRedirectRoutes)) {
					$this->Route->create();
					$this->Route->saveAll($newRedirectRoutes);
				}
			}
		} else {
			$this->Session->setFlash(__d('cms', 'The Url <strong>%s</strong> cannot be deleted. Please create another Url first.', array($route['Route']['url'])), 'default', array('class' => 'error'));
		}

		$this->set(array(
			'routes' => $this->Route->find('all', array(
				'conditions' => array(
					Route::PAGE_KEY => $route[$this->Route->alias][Route::PAGE_KEY],
					Route::LANG_KEY => $route[$this->Route->alias][Route::LANG_KEY]
				),
				'order' => 'Route.url ASC'
			)),
			'routeTypes' => $this->Route->getRouteTypes()
		));
		$this->render('add');
	}
}
