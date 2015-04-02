<?php
/**
 * @var CoreView $this
 */
$bodyCssClass = (array) $this->get('bodyCssClass');
?>
<!doctype html>
<!--[if lt IE 7]>     <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en-US"><![endif]-->
<!--[if IE 7]>        <html class="no-js lt-ie9 lt-ie8" lang="en-US"><![endif]-->
<!--[if IE 8]>        <html class="no-js lt-ie9" lang="en-US"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en-US"><!--<![endif]-->
<head>
	<?php echo $this->element('Core.layout/head'); ?>
</head>
<body<?php echo !empty($bodyCssClass) ? ' class="' . implode(' ', $bodyCssClass) . '"' : '' ?>>
	<div class="support-wrapper">
		<?php echo $this->fetch('content'); ?>
	</div>
</body>
</html>