<?php
/**
 * @var CoreView $this
 */
?>
<?php if (Authenticator::get()): ?>
<div class="user-menu">
	<a href="#"><?php echo Authenticator::get('User.username'); ?></a>
	<ul>
		<li><?php echo $this->CHtml->backendLink(__d('core', 'Edit Profile'), '/profile') ?></li>
		<li><?php echo $this->CHtml->backendLink(__d('core', 'Logout'), '/logout') ?></li>
	</ul>
</div>
<?php endif; ?>