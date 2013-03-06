<!doctype html>
<!--[if lt IE 7]>     <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en-US"><![endif]-->
<!--[if IE 7]>        <html class="no-js lt-ie9 lt-ie8" lang="en-US"><![endif]-->
<!--[if IE 8]>        <html class="no-js lt-ie9" lang="en-US"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en-US"><!--<![endif]-->
<head>
	<?php echo $this->element('Core.head'); ?>
</head>
<body>
	<header>
		<div class="inner">
			<?php if (isset($backend_menu_for_layout)): ?>
			<ul class="main-menu">
				<?php echo $this->Navigation->render($backend_menu_for_layout['primary']); ?>
			</ul>
			<?php endif; ?>
			<?php if (Authenticator::get()): ?>
			<div class="user-menu">
				<a href="#"><?php echo Authenticator::get('User.username'); ?></a>
				<ul>
					<li><?php echo $this->Html->link(__d('core', 'Edit Profile'), array('plugin' => 'core', 'controller' => 'users', 'action' => 'profile')) ?></li>
					<li><?php echo $this->Html->link(__d('core', 'Logout'), array('plugin' => 'core', 'controller' => 'users', 'action' => 'logout')) ?></li>
				</ul>
			</div>
			<?php endif; ?>
			<?php echo $this->element('Core.language_switch'); ?>
		</div>
	</header>
	<div id="main">
		<div class="inner">
			<?php if (isset($backend_menu_for_layout)): ?>
			<ul class="sub-menu">
				<?php echo $this->Navigation->render($backend_menu_for_layout['secondary']); ?>
			</ul>
			<?php endif; ?>
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
	</div>
	<?php echo $this->element('Core.footer'); ?>
</body>
</html>