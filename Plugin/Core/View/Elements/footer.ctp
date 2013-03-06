<footer>
	<div class="inner">
		Wasabi CMS
		<?php if (Configure::read('debug') > 0) {
			echo $this->Html->link('Go to Tests', '/test.php');
		} ?>
	</div>
</footer>
<?php
echo $this->WasabiAsset->js('/js/jquery-1.9.1.min.js', 'Core');
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
<!--[if lt IE 7 ]>
<script defer src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
<script defer>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
<![endif]-->
<?php echo $this->element('sql_dump'); ?>