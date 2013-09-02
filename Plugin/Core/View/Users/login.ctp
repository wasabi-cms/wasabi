<?php
/**
 * @var CoreView $this
 */
?>
<?php
echo $this->Html->image('/core/img/wasabi.png', array());
if (Configure::read('Settings.Core.Login.Message.show') === '1' && ($message = Configure::read('Settings.Core.Login.Message.text'))) {
	$class = Configure::read('Settings.Core.Login.Message.class');
?>
	<div class="msg-box <?php echo $class ? $class : 'info' ?>">
		<?php echo $message; ?>
	</div>
<?php }
echo $this->Form->create('User', array('url' => array('plugin' => 'core', 'controller' => 'users', 'action' => 'login')));
echo $this->Session->flash();
?>
<div class="support-content">
	<?php
	echo $this->Form->input('User.username', array('label' => __d('core', 'Username').':'));
	echo $this->Form->input('User.password', array('label' => __d('core', 'Password').':'));
	echo $this->Form->input('User.remember', array(
		'label' => __d('core', 'Remember me for 2 weeks'),
		'type' => "checkbox"
	)); ?>
</div>
<div class="form-controls">
	<?php echo $this->Form->button('<span>' . __d('core', 'Login') . '</span>', array('class' => 'button')); ?>
</div>
<?php echo $this->Form->end(); ?>
<div class="bottom-links">
	<?php #empty for now ?>
</div>