<?php
/**
 * @var CoreView $this
 */
?>
<?php echo $this->Form->create('Install', array('url' => array('plugin' => 'core', 'controller' => 'core_install', 'action' => 'config'), 'novalidate')); ?>
	<div class="install-content">
		<?php echo $this->Session->flash(); ?>
		<ul class="progress">
			<li class="done">1</li>
			<li class="done">2</li>
			<li class="active">3</li>
		</ul>
		<h2><?php echo __d('core', 'Step 3: Additional Configuration Options') ?></h2>
		<div class="form-content">
			<?php
			echo $this->CForm->input('cookie_name', array('label' => __d('core', 'Cookie name').':', 'info' => __d('core', 'The default name of the Session / Cookie.<br>Change it to match your application name or domain to prevent other people from directly seeing that this site is using Wasabi.'), 'default' => 'Wasabi'));
			echo $this->CForm->input('pygmentize_path', array('label' => __d('core', 'pygmentize'), 'info' => __d('core', '<a href="http://pygments.org" target="_blank">Pygments</a> is a generic syntax highlighter written in python. Provide the full path here if you want to use it. (leave this field blank if unknown)<br><br>e.g. on Windows: C:\Python27\Scripts\pygmentize.exe<br>e.g. on Linux: /usr/bin/pygmentize'), 'placeholder' => 'full path to pygmentize(.exe)'));
			echo $this->CForm->input('pngcrush_path', array('label' => __d('core', 'pngcrush'), 'info' => __d('core', '<a href="http://pmt.sourceforge.net/pngcrush/" target="_blank">Pngcrush</a> is an optimizer for PNG files. Provide the full path here if you want to use it. (leave this field blank if unknown)<br><br>e.g. on Windows: C:\www\libs\pngcrush.exe<br>e.g. on Linux: /usr/bin/pngcrush'), 'placeholder' => 'full path to pngcrush(.exe)'));
			?>
		</div>
	</div>
	<div class="form-actions-bar">
		<?php echo $this->Form->button(__d('core', 'Finalize'), array('class' => 'button green primary')); ?>
	</div>
<?php echo $this->Form->end(); ?>