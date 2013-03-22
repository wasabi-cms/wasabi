<?php /** @var CoreView $this */ ?>
<!doctype html>
<!--[if lt IE 7]>     <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en-US"><![endif]-->
<!--[if IE 7]>        <html class="no-js lt-ie9 lt-ie8" lang="en-US"><![endif]-->
<!--[if IE 8]>        <html class="no-js lt-ie9" lang="en-US"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en-US"><!--<![endif]-->
<head>
	<?php echo $this->element('Core.head'); ?>
</head>
<body>
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
<!--[if lt IE 7 ]>
<script defer src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
<script defer>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
<![endif]-->
<?php echo $this->element('sql_dump'); ?>
</body>
</html>