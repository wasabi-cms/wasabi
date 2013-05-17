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
<?php echo $this->element('Core.head'); ?>
</head>
<body>
	<header<?php echo (Configure::read('debug') > 0) ? ' class="debug"' : '' ?>>
		<div class="row full">
			<?php echo $this->element('Core.menu_primary'); ?>
			<?php echo $this->element('Core.menu_user'); ?>
			<?php echo $this->element('Core.language_switch'); ?>
		</div>
	</header>
	<div id="main">
		<div class="row full">
			<?php echo $this->element('Core.menu_secondary'); ?>
		</div>
		<?php if (CakeSession::check('Message.flash')): ?>
		<div class="row full">
			<?php echo $this->Session->flash(); ?>
		</div>
		<?php endif; ?>
		<div class="row">
			<?php echo $this->fetch('content'); ?>
		</div>
	</div>
	<?php echo $this->element('Core.footer'); ?>
</body>
</html>