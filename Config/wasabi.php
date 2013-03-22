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
 * @subpackage    Wasabi.Config
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * This is the prefix for backend urls.
 * By default all backend urls are prefixed with 'backend',
 * e.g. http://yourdomain.com/backend/users
 */
Configure::write('Wasabi.backend_prefix', 'backend');

/**
 * Pygmentize is a python library to transform code blocks into nice format.
 * Provide the full path to the library here if you want to use it.
 */
Configure::write('Wasabi.pygmentize_path', 'full_path_to_pygmentize');
