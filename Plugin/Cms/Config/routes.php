<?php
/**
 * Core routes
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the below copyright notice.
 *
 * @copyright     Copyright 2013, Frank Förster (http://frankfoerster.com)
 * @link          http://github.com/frankfoerster/wasabi
 * @package       Wasabi
 * @subpackage    Wasabi.Plugin.Cms.Config
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$prefix = Configure::read('Wasabi.backend_prefix');

// Pages
Router::connect("/${prefix}/cms/pages", array('plugin' => 'cms', 'controller' => 'cms_pages', 'action' => 'index'));
Router::connect("/${prefix}/cms/pages/:action/*", array('plugin' => 'cms', 'controller' => 'cms_pages'));

// Modules
Router::connect("/${prefix}/cms/modules", array('plugin' => 'cms', 'controller' => 'cms_modules', 'action' => 'index'));
Router::connect("/${prefix}/cms/modules/:action/*", array('plugin' => 'cms', 'controller' => 'cms_modules'));

// Settings
Router::connect("/${prefix}/cms/settings/edit", array('plugin' => 'cms', 'controller' => 'cms_settings', 'action' => 'edit'));

// Routes
Router::connect("/${prefix}/cms/routes", array('plugin' => 'cms', 'controller' => 'cms_routes', 'action' => 'index'));
Router::connect("/${prefix}/cms/routes/:action/*", array('plugin' => 'cms', 'controller' => 'cms_routes'));

// Frontend Preview
Router::connect("/page/:id/:lang_id/:preview", array('plugin' => 'cms', 'controller' => 'cms_pages_frontend', 'action' => 'view'), array('pass' => array('id', 'lang_id', 'preview')));
