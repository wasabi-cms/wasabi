<?php
/**
 * @var CoreView $this
 */
if (Authenticator::get()): ?>
	<li class="user-menu dropdown">
		<a data-toggle="dropdown" href="javascript:void(0)"><?php echo Authenticator::get('User.username'); ?><i class="icon-angle-down"></i></a>
		<ul class="dropdown-menu pull-right">
			<li><?php echo $this->Html->backendUnprotectedLink(__d('core', 'Edit Profile'), '/profile') ?></li>
			<li><?php echo $this->Html->backendUnprotectedLink(__d('core', 'Logout'), '/logout') ?></li>
		</ul>
	</li>
<?php endif; ?>