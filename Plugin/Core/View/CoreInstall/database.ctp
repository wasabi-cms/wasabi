<?php
/**
 * @var CoreView $this
 */
?>
<ul class="progress">
	<li class="active">1</li>
	<li>2</li>
	<li>3</li>
</ul>
<?php echo $this->Form->create('CoreInstall', array('url' => array('plugin' => 'core', 'controller' => 'core_install', 'action' => 'database'), 'novalidate')); ?>
<div class="install-content">
	<h2><?php echo __d('core', 'Step 1: Database Setup') ?></h2>
	<?php echo $this->Session->flash(); ?>
	<div class="form-content">
		<?php
		echo $this->CForm->input('host', array('label' => __d('core', 'Host').':', 'type' => 'text', 'default' => 'localhost'));
		echo $this->CForm->input('login', array('label' => __d('core', 'User / Login').':', 'type' => 'text', 'default' => 'root'));
		echo $this->CForm->input('password', array('label' => __d('core', 'Password').':', 'type' => 'password'));
		echo $this->CForm->input('database', array('label' => __d('core', 'Database').':', 'type' => 'text', 'default' => 'wasabi'));
		echo $this->CForm->input('prefix', array('label' => __d('core', 'Prefix').':', 'type' => 'text', 'info' => __d('core', 'useful if you share your database with other applications')));
		echo $this->CForm->input('port', array('label' => __d('core', 'Port').':', 'type' => 'text', 'info' => __d('core', 'leave blank if unknown')));
		?>
	</div>
</div>
<div class="form-controls">
	<?php echo $this->Form->button(__d('core', 'Continue'), array('class' => 'button')); ?>
</div>
<?php echo $this->Form->end(); ?>