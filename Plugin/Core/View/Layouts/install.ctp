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
	<?php
	echo $this->element('Core.layout/head');
	echo $this->WasabiAsset->css('/css/install.css', 'Core');
	?>
</head>
<body class="install">
<div class="install-wrapper">
	<div class="round-shadow">
		<h1><?php echo __d('core', 'Installing Wasabi') ?></h1>
		<?php echo $this->fetch('content'); ?>
	</div>
</div>
<?php echo $this->element('Core.layout/footer', array('sql_dump' => false)); ?>
</body>
</html>