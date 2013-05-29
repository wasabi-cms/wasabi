<?php
/**
 * @var CoreView $this
 */
?>
<footer class="row">
	Wasabi CMS
	<?php if (Configure::read('debug') > 0) {
		echo $this->Html->link('Go to Tests', '/test.php');
	} ?>
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
<?php if (!isset($sql_dump) || $sql_dump !== false): ?>
<div class="row">
	<?php echo $this->element('sql_dump'); ?>
</div>
<?php endif; ?>