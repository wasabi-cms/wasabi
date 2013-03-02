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
 * @subpackage    Wasabi.Plugin.Core.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('BackendAppController', 'Core.Controller');

/**
 * @property Language $Language
 * @property array $data
 */

class LanguagesController extends BackendAppController {

	/**
	 * Models used by this controller
	 *
	 * @var array
	 */
	public $uses = array(
		'Core.Language'
	);

	/**
	 * Index action
	 *
	 * @return void
	 */
	public function index() {
		$languages = $this->Language->findAll();
		$this->set('languages', $languages);
	}

	/**
	 * Add action
	 * GET | POST
	 *
	 * @return void
	 */
	public function add() {
		$this->set('title_for_layout', __d('core', 'Add a new Language'));
		if ($this->request->is('post') && !empty($this->data)) {
			$this->request->data['Language']['position'] = 9999;
			if ($this->Language->save($this->data)) {
				$this->Session->setFlash(__d('core', 'The language <strong>%s</strong> has been added.', array($this->data['Language']['name'])), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		}
	}

	/**
	 * Edit action
	 * GET | POST
	 *
	 * @param null|integer $id
	 * @return void
	 */
	public function edit($id = null) {
		if ($id === null) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('title_for_layout', __d('core', 'Edit Language'));
		if (!$this->request->is('post') && empty($this->data)) {
			$this->request->data = $this->Language->findById($id);
		} else {
			if ($this->Language->save($this->data)) {
				$this->Session->setFlash(__d('core', 'The language <strong>%s</strong> has been updated successfully.', array($this->data['Language']['name'])), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		}
		$this->render('add');
	}

	/**
	 * Delete action
	 * POST
	 *
	 * @param null|integer $id
	 * @return void
	 * @throws MethodNotAllowedException
	 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

		if ($id === null || !$this->Language->canBeDeleted($id)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		if ($this->Language->delete($id)) {
			$this->Session->setFlash(__d('core', 'The language has been deleted.'), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}

		$this->Session->setFlash(__d('core', 'The language has not been deleted.'), 'default', array('class' => 'error'));
		$this->redirect(array('action' => 'index'));
	}
}
