<?php
/**
 * @var CmsView $this
 */

$this->extend('Core.default');

$this->append('head_css');
echo $this->WasabiAsset->css('/css/cms.css', 'Cms');
$this->end();

$this->append('bottom_js');
//echo $this->WasabiAsset->js('/js/cms.js', 'Cms');?>
<script>
	require.config({
		paths : {
			Cms: "<?php echo (Configure::read('debug') === 0) ? '/cms/js' : '/cms/js/src' ?>",
			cms: "<?php echo (Configure::read('debug') === 0) ? '/cms/js/cms.min' : '/cms/js/src/cms' ?>"
		}
	});
	require(['common'], function() {
		require(['cms'], function(Cms) {
			new Cms({
				<?php echo $this->fetch('Cms.preload') ?>
			}).init();
			<?php echo $this->fetch('Cms.requireJs') ?>
		});
	});
</script>
<?php
$this->end();

echo $this->fetch('content');
