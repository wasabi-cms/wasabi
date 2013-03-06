<?php if (Authenticator::get()): ?>
<div class="user-menu">
	<a href="#"><?php echo Authenticator::get('User.username'); ?></a>
	<ul>
		<li><?php echo $this->Html->link(__d('core', 'Edit Profile'), array('plugin' => 'core', 'controller' => 'users', 'action' => 'profile')) ?></li>
		<li><?php echo $this->Html->link(__d('core', 'Logout'), array('plugin' => 'core', 'controller' => 'users', 'action' => 'logout')) ?></li>
	</ul>
</div>
<?php endif; ?>