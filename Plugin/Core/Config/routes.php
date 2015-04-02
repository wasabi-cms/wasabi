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
 * @subpackage    Wasabi.Plugin.Core.Config
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

// Handle .json and application/json requests
Router::parseExtensions('json');

$prefix = Configure::read('Wasabi.backend_prefix');

// Login & Logout
Router::connect("/${prefix}/login", array('plugin' => 'core', 'controller' => 'users', 'action' => 'login'));
Router::connect("/${prefix}/logout", array('plugin' => 'core', 'controller' => 'users', 'action' => 'logout'));

// Dashboard
Router::connect("/${prefix}", array('plugin' => 'core', 'controller' => 'dashboard', 'action' => 'index'));

// Edit Profile
Router::connect("/${prefix}/profile", array('plugin' => 'core', 'controller' => 'users', 'action' => 'profile'));

// Users
Router::connect("/${prefix}/users", array('plugin' => 'core', 'controller' => 'users', 'action' => 'index'));
Router::connect("/${prefix}/users/:action/*", array('plugin' => 'core', 'controller' => 'users'));

// Groups
Router::connect("/${prefix}/groups", array('plugin' => 'core', 'controller' => 'groups', 'action' => 'index'));
Router::connect("/${prefix}/groups/:action/*", array('plugin' => 'core', 'controller' => 'groups'));

// Languages
Router::connect("/${prefix}/languages", array('plugin' => 'core', 'controller' => 'languages', 'action' => 'index'));
Router::connect("/${prefix}/languages/:action/*", array('plugin' => 'core', 'controller' => 'languages'));

// Core Settings
Router::connect("/${prefix}/settings/:action/*", array('plugin' => 'core', 'controller' => 'core_settings'));

// Plugins
Router::connect("/${prefix}/plugins", array('plugin' => 'core', 'controller' => 'plugins', 'action' => 'index'));
Router::connect("/${prefix}/plugins/:action/*", array('plugin' => 'core', 'controller' => 'plugins'));

// Permissions
Router::connect("/${prefix}/permissions", array('plugin' => 'core', 'controller' => 'permissions', 'action' => 'index'));
Router::connect("/${prefix}/permissions/:action/*", array('plugin' => 'core', 'controller' => 'permissions'));

// Menus
Router::connect("/${prefix}/menus", array('plugin' => 'core', 'controller' => 'menus', 'action' => 'index'));
Router::connect("/${prefix}/menus/:action/*", array('plugin' => 'core', 'controller' => 'menus'));

// Media
Router::connect("/${prefix}/media", array('plugin' => 'core', 'controller' => 'media', 'action' => 'index'));
Router::connect("/${prefix}/media/:action/*", array('plugin' => 'core', 'controller' => 'media'));

// Browser
Router::connect("/${prefix}/browser/not-supported", array('plugin' => 'core', 'controller' => 'browser', 'action' => 'notSupported'));
