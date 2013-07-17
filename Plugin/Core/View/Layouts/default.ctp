<?php
/**
 * @var CoreView $this
 */
?>
<!doctype html>
<!--[if lt IE 7]>     <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en-US"><![endif]-->
<!--[if IE 7]>        <html class="no-js lt-ie9 lt-ie8" lang="en-US"><![endif]-->
<!--[if IE 8]>        <html class="no-js lt-ie9" lang="en-US"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en-US"><!--<![endif]-->
<head>
	<?php echo $this->element('Core.layout/head'); ?>
</head>
<body>
	<header class="row<?php echo (Configure::read('debug') > 0) ? ' debug' : '' ?>">
		<ul class="row">
			<li><a class="brand">wasabi</a></li>
			<li><a class="toggle-nav" href="javascript:void(0)"><i class="icon-reorder"></i></a></li>
			<?php echo $this->element('Core.menus/top_nav'); ?>
			<?php echo $this->element('Core.menus/user'); ?>
			<?php echo $this->element('Core.menus/language'); ?>
		</ul>
	</header>
	<div id="wrapper">
		<aside>
			<?php echo $this->element('Core.menus/main_nav'); ?>
		</aside>
		<div id="content">
			<?php
			echo $this->CHtml->titlePad();
			if (CakeSession::check('Message.flash')) {
				echo '<div class="row">' . $this->Session->flash() . '</div>';
			}
			echo $this->fetch('content');
			echo $this->element('Core.layout/footer');
			?>
		</div>
	</div>
	<?php
	echo $this->WasabiAsset->js('/js/jquery-1.10.2.min.js', 'Core');
	echo $this->element('Core.js_translations');
	echo $this->WasabiAsset->js('/js/plugins.js', 'Core');
	echo $this->WasabiAsset->js('/js/script.js', 'Core');
	if ($this->request->params['plugin'] !== 'core' && file_exists(CakePlugin::path(Inflector::camelize($this->request->params['plugin'])) . 'webroot' . DS . 'js' . DS . 'plugins.js')) {
		echo $this->WasabiAsset->js('/js/plugins.js', Inflector::camelize($this->request->params['plugin']));
	}
	if ($this->request->params['plugin'] !== 'core' && file_exists(CakePlugin::path(Inflector::camelize($this->request->params['plugin'])) . 'webroot' . DS . 'js' . DS . 'script.js')) {
		echo $this->WasabiAsset->js('/js/script.js', Inflector::camelize($this->request->params['plugin']));
	}
	$this->start('bottom_js');
	$this->end('bottom_js');
	echo $this->fetch('bottom_js');
	?>
</body>
</html>