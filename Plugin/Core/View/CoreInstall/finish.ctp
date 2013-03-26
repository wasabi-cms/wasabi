<?php
/**
 * @var CoreView $this
 * @var boolean $canBeInstalled
 * @var array $checks
 */
?>
<div class="install-content">
	<?php echo $this->Session->flash(); ?>
	<ul class="progress">
		<li class="done">1</li>
		<li class="done">2</li>
		<li class="done">3</li>
	</ul>
	<h2><?php echo __d('core', 'Congratulations!') ?></h2>
	<div class="checks">
		<p class="check success"><?php echo __d('core', 'You have completed the basic setup of Wasabi.') ?></p>
	</div>
	<h2><?php echo __d('core', 'What’s next?') ?></h2>
	<ul class="whats-next">
		<li><?php echo $this->CHtml->backendLink(__d('core', 'Login to the backend'), '/login'); ?><br><small>(<?php echo __d('core', 'default username: <strong>%s</strong>, default password: <strong>%s</strong>', array('admin', 'admin')); ?>)</small></li>
		<li><?php echo __d('core', 'Change the password of the admin account.'); ?></li>
		<li><?php echo __d('core', 'Install additional <a href="https://github.com/Wasabi-Plugins" target="_blank">plugins</a>'); ?>.</li>
	</ul>
</div>