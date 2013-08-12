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
	<?php echo $this->element('Core.layout/header'); ?>
	<div id="wrapper">
		<div id="asidebg"></div>
		<aside>
			<?php echo $this->element('Core.menus/main_nav'); ?>
		</aside>
		<div id="content">
			<?php
			echo $this->CHtml->titlePad();
			if (CakeSession::check('Message.flash')) {
				echo $this->Session->flash();
			}
			echo $this->fetch('content');
			?>
		</div>
		<?php echo $this->element('Core.layout/footer'); ?>
	</div>
	<?php
	$this->start('bottom_body');
	$this->end('bottom_body');
	echo $this->fetch('bottom_body');
	echo $this->WasabiAsset->js('/js/jquery-1.10.2.min.js', 'Core');
	echo $this->element('Core.js_translations');
	echo $this->WasabiAsset->js('/js/plugins.js', 'Core');
	echo $this->WasabiAsset->js('/js/script.js', 'Core');
	$this->start('bottom_js');
	$this->end('bottom_js');
	echo $this->fetch('bottom_js');
	?>
</body>
</html>