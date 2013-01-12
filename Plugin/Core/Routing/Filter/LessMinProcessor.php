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
			$less_dir = new Folder($webroot . 'less', false);
			$this->processLessFiles($less_dir, $webroot);
		}
		// process *.less files of the app itself
		$webroot = APP . WEBROOT_DIR . DS;
		$less_dir = new Folder($webroot . 'less');
		$this->processLessFiles($less_dir, $webroot);
		return null;
	}

/**
 * Process all *.less files in $less_dir and minify them afterwards.
 * Corresponding *.css files are saved in app/webroot/css or app/Plugin/PluginName/webroot/css
 * depending on the specified Less directory and webroot.
 *
 * @param Folder $less_dir folder holding the *.less files to be processed
 * @param string $webroot absolute path to webroot with trailing DS
 * @return void
 */
	public function processLessFiles(Folder $less_dir, $webroot) {
		foreach ($less_dir->find('.*\.less') as $less_file) {
			$less_info = pathinfo($less_file);
			$css_file = $webroot . 'css' . DS . $less_info['filename'] . '.css';
			$less_file = $less_dir->path . DS . $less_file;
			// automatically create the css file and its folder if it does not exist
			if (!file_exists($css_file)) {
				$created_css_file = new File($css_file, true, 0755);
				// set the creation date to way in the past
				touch($created_css_file->path, 0);
			}
			if (lessc::ccompile($less_file, $css_file)) {
				// only minify the css file if less compilation was neccessary (file modified)
				$css_min = new CSSmin();
				$min = $css_min->run(file_get_contents($css_file));
				file_put_contents($css_file, $min);
			}
		}
	}

}
