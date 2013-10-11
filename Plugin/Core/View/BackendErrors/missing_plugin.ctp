<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Errors
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<h2><?php echo __d('cake_dev', 'Missing Plugin'); ?></h2>
<p class="error">
	<strong><?php echo __d('cake_dev', 'Error'); ?>: </strong>
	<?php echo __d('cake_dev', 'The application is trying to load a file from the %s plugin', '<em>' . $plugin . '</em>'); ?>
</p>
<p class="error">
	<strong><?php echo __d('cake_dev', 'Error'); ?>: </strong>
	<?php echo __d('cake_dev', 'Make sure your plugin %s is in the ' . APP_DIR . DS . 'Plugin directory and was loaded', $plugin); ?>
</p>
<pre>
&lt;?php
CakePlugin::load('<?php echo $plugin?>');

</pre>
<p class="notice">
	<strong><?php echo __d('cake_dev', 'Loading all plugins'); ?>: </strong>
	<?php echo __d('cake_dev', 'If you wish to load all plugins at once, use the following line in your ' . APP_DIR . DS . 'Config' . DS . 'bootstrap.php file'); ?>
</p>
<pre>
CakePlugin::loadAll();
</pre>
<?php echo $this->element('exception_stack_trace'); ?>