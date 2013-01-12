<?php
/**
 * This bootstrap file is used as an entry point for IDE integrated Unit Tests with PHPUnit.
 * It sets up all required named constants and include paths.
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the below copyright notice.
 *
 * @copyright     Copyright 2013, Frank Förster (http://frankfoerster.com)
 * @link          http://github.com/frankfoerster/wasabi
 * @package       Wasabi
 * @subpackage    Wasabi.Test
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

set_time_limit(0);
ini_set('display_errors', 1);

/**
 * Use the DS to separate the directories in other defines
 *
 */
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

/**
 * The full path to the directory which holds "app", WITHOUT a trailing DS.
 *
 */
if (!defined('ROOT')) {
	define('ROOT', dirname(dirname(dirname(__FILE__))));
}

/**
 * The actual directory name for the "app".
 *
 */
if (!defined('APP_DIR')) {
	define('APP_DIR', basename(dirname(dirname(__FILE__))));
}

/**
 * The absolute path to the "Cake" directory, WITHOUT a trailing DS.
 *
 * Edit this path if your Cake lib resides in another directory than the default.
 */
//define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'lib');

/**
 * Editing below this line should not be necessary.
 * Change at your own risk.
 *
 */
if (!defined('WEBROOT_DIR')) {
  define('WEBROOT_DIR', 'webroot');
}

if (!defined('WWW_ROOT')) {
  define('WWW_ROOT', ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS);
}

if (!defined('CAKE_CORE_INCLUDE_PATH')) {
  if (function_exists('ini_set')) {

    ini_set('include_path', ROOT . DS . 'lib' . PATH_SEPARATOR . ini_get('include_path'));
  }
  if (!include ('Cake' . DS . 'bootstrap.php')) {
    $failed = true;
  }
} else {
  if (!include (CAKE_CORE_INCLUDE_PATH . DS . 'Cake' . DS . 'bootstrap.php')) {
    $failed = true;
  }
}
if (!empty($failed)) {
  trigger_error("CakePHP core could not be found.  Check the value of CAKE_CORE_INCLUDE_PATH in APP/webroot/index.php.  It should point to the directory containing your " . DS . "cake core directory and your " . DS . "vendors root directory.", E_USER_ERROR);
}
