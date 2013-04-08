<?php
/**
 * LessMinProcessor compiles *.less files to *.css and minifies them
 * for all *.less files  in app/webroot/less/ and app/Plugin/../webroot/less/
 * css files are written to app/webroot/css/  and app/Plugin/../webroot/css/
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the below copyright notice.
 *
 * @copyright     Copyright 2013, Frank Förster (http://frankfoerster.com)
 * @link          http://github.com/frankfoerster/wasabi
 * @origin        http://github.com/frankfoerster/LessMin
 * @package       Wasabi
 * @subpackage    Wasabi.Plugin.Core.Routing.Filter
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('DispatcherFilter', 'Routing');
App::uses('CakePlugin', 'Core');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::import('Vendor', 'Core.Less');
App::import('Vendor', 'Core.CssMin');

class LessMinProcessor extends DispatcherFilter {

	/**
	 * Priority value
	 *
	 * @var int
	 * @see http://book.cakephp.org/2.0/en/development/dispatch-filters.html
	 */
	public $priority = 9;

	/**
	 * beforeDispatch middleware entry point
	 *
	 * @param CakeEvent $event
	 * @return string|null
	 */
	public function beforeDispatch(CakeEvent $event) {
		// check DEBUG Level and SKIP_ON_PRODUCTION setting
		if (Configure::read('debug') === 0 && Configure::read('LessMin.SKIP_ON_PRODUCTION') === true) {
			return 'skipped';
		}
		// process *.less files of all plugins
		$plugins = CakePlugin::loaded();
		foreach ($plugins as $plugin) {
			$webroot = CakePlugin::path($plugin) . 'webroot' . DS;
			$lessDir = new Folder($webroot . 'less', false);
			$this->processLessFiles($lessDir, $webroot);
		}
		// process *.less files of the app itself
		$webroot = APP . WEBROOT_DIR . DS;
		$lessDir = new Folder($webroot . 'less');
		$this->processLessFiles($lessDir, $webroot);
		return null;
	}

	/**
	 * Process all *.less files in $lessDir and minify them afterwards.
	 * Corresponding *.css files are saved in app/webroot/css or app/Plugin/PluginName/webroot/css
	 * depending on the specified Less directory and webroot.
	 *
	 * @param Folder $lessDir folder holding the *.less files to be processed
	 * @param string $webroot absolute path to webroot with trailing DS
	 * @return void
	 */
	public function processLessFiles(Folder $lessDir, $webroot) {
		foreach ($lessDir->find('.*\.less') as $lessFile) {
			$lessInfo = pathinfo($lessFile);
			$cssFile = $webroot . 'css' . DS . $lessInfo['filename'] . '.css';
			$lessFile = $lessDir->path . DS . $lessFile;
			// automatically create the css file and its folder if it does not exist
			if (!file_exists($cssFile)) {
				$createdCssFile = new File($cssFile, true, 0755);
				// set the creation date to way in the past
				touch($createdCssFile->path, 0);
			}
			if (lessc::ccompile($lessFile, $cssFile)) {
				// only minify the css file if less compilation was neccessary (file modified)
				$cssMin = new CSSmin();
				$min = $cssMin->run(file_get_contents($cssFile));
				file_put_contents($cssFile, $min);
			}
		}
	}

}
