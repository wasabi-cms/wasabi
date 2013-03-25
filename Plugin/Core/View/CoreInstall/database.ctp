<?php
/**
 * @var CoreView $this
 */
?>
<?php echo $this->Form->create('Install', array('url' => array('plugin' => 'core', 'controller' => 'core_install', 'action' => 'database'), 'novalidate')); ?>
<div class="install-content">
	<ul class="progress">
		<li class="active">1</li>
		<li>2</li>
		<li>3</li>
	</ul>
	<h2><?php echo __d('core', 'Step 1: Database Setup') ?></h2>
	<?php echo $this->Session->flash(); ?>
	<div class="form-content">
		<?php
		echo $this->CForm->input('host', array('label' => __d('core', 'Host').':', 'type' => 'text', 'default' => 'localhost', 'error' => false));
		echo $this->CForm->input('login', array('label' => __d('core', 'User / Login').':', 'type' => 'text', 'default' => 'root', 'error' => false));
		echo $this->CForm->input('password', array('label' => __d('core', 'Password').':', 'type' => 'password', 'error' => false));
		echo $this->CForm->input('database', array('label' => __d('core', 'Database').':', 'type' => 'text', 'default' => 'wasabi', 'error' => false));
		echo $this->CForm->input('prefix', array('label' => __d('core', 'Prefix').':', 'type' => 'text', 'info' => __d('core', 'useful if you share your database with other applications'), 'error' => false));
		echo $this->CForm->input('port', array('label' => __d('core', 'Port').':', 'type' => 'text', 'info' => __d('core', 'leave blank if unknown'), 'error' => false));
		?>
	</div>
</div>
<div class="form-actions-bar">
	<?php echo $this->Form->button(__d('core', 'Continue'), array('class' => 'button green primary')); ?>
</div>
<?php echo $this->Form->end(); ?>