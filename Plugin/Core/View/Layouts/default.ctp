<?php
/**
 * @var CoreView $this
 */

$translations = array();
$availableTranslations = WasabiEventManager::trigger(new stdClass(), 'Backend.JS.Translations.load');
foreach ($availableTranslations['Backend.JS.Translations.load'] as $key => $tgroup) {
	$translations = array_merge($translations, $tgroup);
}
$this->start('preload');
echo 'translations: ' . json_encode($translations) . PHP_EOL;
$this->end('preload');

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
	<?php echo $this->element('Core.layout/header'); ?>
	<div id="wrapper">
		<div id="asidebg"></div>
		<aside>
			<?php
			echo $this->element('Core.menus/main_nav', array(), array(
//				'cache' => array(
//					'config' => 'core.main_nav',
//					'key' => Authenticator::get('Group.id') . '_' . md5(serialize($this->request->params))
//				)
			));
			?>
		</aside>
		<div id="content">
			<?php
			echo $this->Html->titlePad();
			if (CakeSession::check('Message.flash')) {
				echo $this->Session->flash();
			}
			echo $this->fetch('content');
			?>
		</div>
		<?php

		$this->start('afterContent');
		$this->end('afterContent');
		echo $this->fetch('afterContent');

		echo $this->element('Core.layout/footer');?>
	</div>
	<?php
	$this->start('bottom_body');
	$this->end('bottom_body');
	echo $this->fetch('bottom_body');
	echo $this->WasabiAsset->js('/js/require.min.js', 'Core');
	?>
	<script>
		require.config({
			baseUrl: "<?php echo Router::url((Configure::read('debug') === 0) ? '/core/js/' : '/core/js/src/') ?>"
		});
<?php if (Configure::read('debug') > 0): ?>
		// bust the cache during development to always reload all files
		require.config({
			urlArgs: "bust=" + (new Date()).getTime()
		});
<?php endif; ?>
		require(['common'], function() {
			require(['core'], function(Core) {
				new Core({
					<?php echo $this->fetch('preload') ?>
				}).init();
				<?php echo $this->fetch('Core.requireJs') ?>
			});
		});
	</script>
<?php
	$this->start('bottom_js');
	$this->end('bottom_js');
	echo $this->fetch('bottom_js');
?>
	<!--[if lt IE 9]><?php echo $this->WasabiAsset->js('/js/ie8-iconfix.js', 'Core') ?><![endif]-->
</body>
</html>
