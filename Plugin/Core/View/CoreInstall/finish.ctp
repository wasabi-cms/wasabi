<?php
/**
 * @var CoreView $this
 * @var boolean $canBeInstalled
 * @var array $checks
 */
?>
<div class="install-content">
	<?php echo $this->Session->flash(); ?>
	<ul class="progress">
		<li class="done">1</li>
		<li class="done">2</li>
		<li class="done">3</li>
	</ul>
	<h2><?php echo __d('core', 'Installation Finished') ?></h2>
</div>
<div class="form-actions-bar">

</div>