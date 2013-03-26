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

App::uses('AppController', 'Controller');
App::uses('ConnectionManager', 'Model');
App::uses('File', 'Utility');
App::uses('Hash', 'Utility');
App::uses('MigrationVersion', 'Migrations.Lib');

/**
 * @property CoreInstall $CoreInstall
 * @property array $data
 */

class CoreInstallController extends AppController {

	/**
	 * Models used by this controller.
	 *
	 * @var array
	 */
	public $uses = array(
		'Core.CoreInstall'
	);

	/**
	 * Default viewClass
	 *
	 * @var string
	 */
	public $viewClass = 'Core.Core';

	/**
	 * beforeFilter callback
	 *
	 * create an empty database.php file to dismiss missing file warning
	 * set the layout to 'install.ctp'
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		/**
		 * TODO: remove this statement as soon as ticket #3724 is closed and a solution is present
		 * @see https://cakephp.lighthouseapp.com/projects/42648/tickets/3724-Add-a-silent-option-to-ConnectionManager-to-avoid-inclusion-of-databasephp
		 */
		$db_config = APP . 'Config' . DS . 'database.php';
		if (!file_exists($db_config)) {
			new File($db_config, true);
		}

		$this->layout = 'install';
	}

	/**
	 * Step 0: Check dependencies
	 * GET
	 *
	 * @return void
	 */
	public function check() {
		$checks = array();

		if (is_writable(TMP)) {
			$checks[] = array(
				'class' => 'success',
				'message' => __d('core', 'Your <strong>/app/tmp</strong> directory is writable.')
			);
		} else {
			$checks[] = array(
				'class' => 'error',
				'message' => __d('core', 'Please make sure your <strong>/app/tmp</strong> directory is writable.')
			);
		}

		if (is_writable(APP . 'Config')) {
			$checks[] = array(
				'class' => 'success',
				'message' => __d('core', 'Your <strong>/app/Config</strong> directory is writable.')
			);
		} else {
			$checks[] = array(
				'class' => 'error',
				'message' => __d('core', 'Please make sure your <strong>/app/Config</strong> directory is writable.')
			);
		}

		$minPHPVersion = '5.3.8';
		$phpVersion = phpversion();
		if (version_compare($phpVersion, $minPHPVersion, '>=')) {
			$checks[] = array(
				'class' => 'success',
				'message' => __d('core', 'PHP version <strong>%s</strong> >= <strong>%s</strong>', array($phpVersion, $minPHPVersion))
			);
		} else {
			$checks[] = array(
				'class' => 'error',
				'message' => __d('core', 'PHP version <strong>%s</strong> < <strong>%s</strong>', array($phpVersion, $minPHPVersion))
			);
		}

		$minCakeVersion = '2.3.1';
		$cakeVersion = Configure::version();
		if (version_compare($cakeVersion, $minCakeVersion, '>=')) {
			$checks[] = array(
				'class' => 'success last',
				'message' => __d('core', 'CakePHP version <strong>%s</strong> >= <strong>%s</strong>', array($cakeVersion, $minCakeVersion))
			);
		} else {
			$checks[] = array(
				'class' => 'error last',
				'message' => __d('core', 'CakePHP version <strong>%s</strong> < <strong>%s</strong>', array($cakeVersion, $minCakeVersion))
			);
		}

		$canBeInstalled = true;
		foreach ($checks as $check) {
			if (strpos($check['class'], 'error') !== false) {
				$canBeInstalled = false;
				break;
			}
		}

		$this->set(array(
			'title_for_layout' => __d('core', 'Installing Wasabi'),
			'canBeInstalled' => $canBeInstalled,
			'checks' => $checks
		));
	}

	/**
	 * Step 1: Setup the database
	 * GET | POST
	 *
	 * @return void
	 */
	public function database() {
		$this->set('title_for_layout', __d('core', 'Installing Wasabi - Step 1 - Database Setup'));
		if ($this->request->is('post')) {
			$this->CoreInstall->set($this->request->data);
			if ($this->CoreInstall->validates()) {
				$defaults = array(
					'datasource' => 'Database/Mysql',
					'persistent' => false,
					'encoding' => 'utf8'
				);
				$valid_keys = array('host', 'login', 'password', 'database', 'prefix', 'port');
				$config = $this->request->data['CoreInstall'];
				foreach ($config as $key => $value) {
					if (!in_array($key, $valid_keys)) {
						unset($config[$key]);
					}
				}
				$config = Hash::merge($defaults, $config);
				try {
					ConnectionManager::create('default', $config);
					/** @var DboSource $db */
					$db = ConnectionManager::getDataSource('default');
				} catch (MissingConnectionException $e) {
					$this->Session->setFlash(__d('core', 'Could not connect to database. Please verify that your connection details below are correct.'), 'default', array('class' => 'error'));
					return;
				}
				if (!$db->isConnected()) {
					$this->Session->setFlash(__d('core', 'Could not connect to database. Please verify that your connection details below are correct.'), 'default', array('class' => 'error'));
					return;
				}
				try {
					unset($config['persistent']);
					$this->_writeConfig('database', $config);
				} catch (Exception $e) {
					$this->Session->setFlash(__d('core', 'Could not write configs to database.php.'), 'default', array('class' => 'error'));
					return;
				}
				$this->Session->setFlash(__d('core', 'Your <strong>database connection</strong> is working.'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'import'));
			} else {
				$this->Session->setFlash(__d('core', 'Please correct the marked errors.'), 'default', array('class' => 'error'));
			}
		}
	}

	/**
	 * Step 2: Import Data
	 * GET | POST
	 *
	 * @return void
	 */
	public function import() {
		$this->set('title_for_layout', __d('core', 'Installing Wasasbi - Step 2 - Data Import'));
		if ($this->request->is('post')) {
			try {
				$migration = new MigrationVersion(array(
					'autoinit' => true
				));
				$messages[] = __d('core', 'Migration tables');
				$migrations_map = $migration->getMapping('Core');
				end($migrations_map);
				$latest_version = key($migrations_map);
				$migration->run(array(
					'type' => 'Core',
					'direction' => 'up',
					'version' => $latest_version
				));
			} catch (MigrationVersionException $e) {
				$this->Session->setFlash(__d('core', 'Something went wrong migrating the database: %s', array($e->getMessage())), 'default', array('class' => 'error'));
				return;
			}
			$this->Session->setFlash(__d('core', 'The <strong>data import</strong> completed successfully.'), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'config'));
		}
	}

	/**
	 * Step 3: Additional Configuration Options
	 * GET | POST
	 *
	 * @return void
	 */
	public function config() {
		$this->set('title_for_layout', __d('core', 'Installing Wasabi - Step 3 - Additional Configuration Options'));
		if ($this->request->is('post')) {
			$this->CoreInstall->set($this->request->data);
			$config = $this->request->data['CoreInstall'];
			if (isset($config['pygmentize_path'])
				&& $config['pygmentize_path'] != ''
				&& !is_executable($config['pygmentize_path'])
			) {
				$this->CoreInstall->invalidate('pygmentize_path', __d('core', 'Pygmentize is not executable.'));
			}
			if (isset($config['pngcrush_path'])
				&& $config['pngcrush_path'] != ''
				&& !is_executable($config['pngcrush_path'])
			) {
				$this->CoreInstall->invalidate('pngcrush_path', __d('core', 'Pngcrush is not executable.'));
			}
			if ($this->CoreInstall->validates()) {
				try {
					if (!isset($config['pygmentize_path']) || $config['pygmentize_path'] == '') {
						$config['pygmentize_path'] = 'full_path_to_pygmentize';
					}
					if (!isset($config['pngcrush_path']) || $config['pngcrush_path'] == '') {
						$config['pngcrush_path'] = 'full_path_to_pngcrush';
					}
					$this->_writeConfig('wasabi', $config);
				} catch (Exception $e) {
					$this->Session->setFlash(__d('core', 'Could not write configs to wasabi.php.'), 'default', array('class' => 'error'));
					return;
				}
				try {
					$config = array(
						'salt' => $this->_generateSecurityHash(),
						'cipher' => $this->_generateSecurityHash(40, true)
					);
					$this->_writeConfig('security', $config);
				} catch (Exception $e) {
					$this->Session->setFlash(__d('core', 'Could not write configs to security.php.'), 'default', array('class' => 'error'));
					return;
				}
				$installed = new File(APP . 'Config' . DS . '.installed', true);
				if ($installed->exists()) {
					$this->Session->setFlash(__d('core', 'The setup of all <strong>config files</strong> succeeded.'), 'default', array('class' => 'success'));
					$this->redirect(array('action' => 'finish')); return;
				}
				$this->Session->setFlash(__d('core', 'The Installation could not be finished.'), 'default', array('class' => 'error'));
				$this->redirect(array('action' => 'check')); return;
			} else {
				$this->Session->setFlash(__d('core', 'Please correct the marked errors.'), 'default', array('class' => 'error'));
			}
		}
	}

	/**
	 * Installation Finished
	 *
	 * @return void
	 */
	public function finish() {
		$this->set('title_for_layout', __d('core', 'Wasabi Installation Finished!'));
	}

	/**
	 * Write given configuration parameters to a config file.
	 *
	 * The value of $config['test_param'] replaces {{TEST_PARAM}} in the
	 * resulting configuration file.
	 *
	 * @param string $name the name of the config file without file ending
	 * @param array $config configuration parameters that are transformed to uppercase and
	 * used as replacement keys
	 * @return void
	 * @throws Exception
	 */
	protected function _writeConfig($name, $config) {
		$replacements = array();
		foreach ($config as $key => $value) {
			$replacement_key = '{{' . strtoupper($key) . '}}';
			$replacements[$replacement_key] = $value;
		}

		$install = new File(APP . 'Config' . DS . $name . '.php.install', false);
		$install_content = $install->read();
		$install->close();

		$final_content = strtr($install_content, $replacements);
		$final = new File(APP . 'Config' . DS . $name . '.php', true);
		if (!$final->write($final_content)) {
			throw new Exception();
		}
		$final->close();
	}

	/**
	 * Generate a security hash 0-9a-zA-Z or 0-9 only
	 * with specified length.
	 *
	 * @param integer $length char length of the resulting hash
	 * @param boolean $numbersOnly only use numbers 0-9
	 * @return string
	 */
	protected function _generateSecurityHash($length = 40, $numbersOnly = false) {
		$hash = '';

		$char_list = '0123456789';
		if (!$numbersOnly) {
			$alpha = 'abcdefghijklmnopqrstuvwxyz';
			$char_list .= $alpha . strtoupper($alpha);
		}

		for ($i = 0; $i < $length; $i++) {
			$pos = round(mt_rand(0, strlen($char_list)));
			$hash .= substr($char_list, $pos, 1);
		}

		return $hash;
	}

}
