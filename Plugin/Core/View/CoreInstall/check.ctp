<?php
/**
 * @var CoreView $this
 * @var boolean $canBeInstalled
 * @var array $checks
 */
?>
<ul class="progress">
	<li>1</li>
	<li>2</li>
	<li>3</li>
</ul>
<div class="install-content row">
	<h2><?php echo __d('core', 'Welcome to the Wasabi Install Tool') ?></h2>
	<p><?php echo __d('core', 'Wasabi is a powerful framework that sits ontop of CakePHP and comes with several enterprise ready Plugins for Content Management and Blogging in multiple languages.') ?></p>
	<p><?php echo __d('core', 'Be ready in 3 simple steps!') ?></p>
	<h2><?php echo __d('core', 'Pre installation checks') ?></h2>
	<div class="checks">
		<?php foreach ($checks as $check): ?>
		<div class="check <?php echo $check['class'] ?>">
			<?php echo $check['message']; ?>
		</div>
		<?php endforeach; ?>
	</div>
</div>
<div class="form-controls">
	<?php echo $this->Html->link(__d('core', 'Start Installation'), '/backend/install/database', array('class' => 'button')) ?>
</div>