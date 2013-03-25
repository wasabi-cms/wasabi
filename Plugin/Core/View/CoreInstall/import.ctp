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
		<li class="active">2</li>
		<li>3</li>
	</ul>
	<h2><?php echo __d('core', 'Step 2: Data Import') ?></h2>
	<p><?php echo __d('core', 'Now it’s time to setup all required database tables and import some default data.') ?></p>
	<div class="infobox">
		<p><?php echo __d('core', 'Beware that existing tables may be overriden during the import.') ?></p>
		<p><?php echo __d('core', 'If you want to keep your existing data, please make sure to backup your database before starting the import.') ?></p>
	</div>
</div>
<?php echo $this->Form->create('Install', array('url' => array('plugin' => 'core', 'controller' => 'core_install', 'action' => 'import'))); ?>
<div class="form-actions-bar">
	<?php echo $this->Form->button(__d('core', 'Start Import'), array('div' => false, 'class' => 'button green primary')); ?>
</div>
<?php echo $this->Form->end(); ?>