<!doctype html>
<!--[if lt IE 7]>     <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en-US"><![endif]-->
<!--[if IE 7]>        <html class="no-js lt-ie9 lt-ie8" lang="en-US"><![endif]-->
<!--[if IE 8]>        <html class="no-js lt-ie9" lang="en-US"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en-US"><!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo $title_for_layout; ?></title>
	<meta name="viewport" content="width=device-width">
	<?php
	echo $this->fetch('meta');
	echo $this->Html->css('/core/css/app');
	if (Configure::read('debug') > 0) {
		echo $this->Html->css('/core/css/debug');
	}
	echo $this->Html->meta('icon');
	?>
</head>
<body>
	<header>
		<div class="inner">
			<?php if (isset($backend_menu_for_layout)): ?>
			<ul class="main-menu">
				<?php echo $this->Navigation->render($backend_menu_for_layout['primary']); ?>
			</ul>
			<?php endif; ?>
			<div class="user-menu">
				<a href="#"><?php echo Authenticator::get('User.username'); ?></a>
				<ul></ul>
			</div>
			<ul class="lang-switcher"></ul>
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
	<footer>
		<div class="inner">
			Wasabi CMS
		</div>
	</footer>
	<?php
	echo $this->Html->script(array(
		'/core/js/jquery-1.8.3.min',
		'/core/js/plugins'
	));
	$this->start('bottom_js');
	$this->end('bottom_js');
	echo $this->fetch('bottom_js');

	if (file_exists(CakePlugin::path(Inflector::camelize($this->request->params['plugin'])) . 'webroot' . DS . 'js' . DS . 'plugins.js')) {
		echo $this->Html->script('/' . $this->request->params['plugin'] . '/js/plugins');
	}
	if (file_exists(CakePlugin::path(Inflector::camelize($this->request->params['plugin'])) . 'webroot' . DS . 'js' . DS . 'script.js')) {
		echo $this->Html->script('/' . $this->request->params['plugin'] . '/js/script');
	}
	?>
	<!--[if lt IE 7 ]>
	<script defer src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
	<script defer>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
	<![endif]-->
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>