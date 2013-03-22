<?php /** @var CoreView $this */ ?>
<div class="round-shadow">
	<div class="title-pad">
		<h1><?php echo __d('core', 'Edit Core Settings') ?></h1>
	</div>
	<?php echo $this->Form->create('CoreSetting'); ?>
	<div class="page-content form-content">
		<?php
		echo $this->Form->input('id', array('type' => 'hidden'));
		?>
		<div class="form-row-wrapper">
			<?php
			echo $this->CForm->input('application_name', array('label' => __d('core', 'Application Name').':', 'info' => __d('core', 'The application name is used to identify your app. (e.g. page title at backend)')));
			echo $this->CForm->input('enable_caching', array('label' => __d('cms', 'Enable Caching?'), 'info' => __d('core', 'Global Cache is used as a default setting and can be overriden by individual plugins.'), 'options' => array(0 => __d('core', 'No'), 1 => __d('core', 'Yes')), 'empty' => false));
			echo $this->CForm->input('cache_time', array('label' => __d('core', 'Cache Time'), 'info' => __d('core', 'Global Cache Time is used as a default and can be overriden by individual plugins.'), 'options' => array(
				'1 hour' => __d('core', '1 hour'),
				'2 hours' => __d('core', '%s hours', array(2)),
				'4 hours' => __d('core', '%s hours', array(4)),
				'8 hours' => __d('core', '%s hours', array(8)),
				'16 hours' => __d('core', '%s hours', array(16)),
				'1 day' => __d('core', '1 day'),
				'2 days' => __d('core', '%s days', array(2)),
				'5 days' => __d('core', '%s days', array(5)),
				'7 days' => __d('core', '%s days', array(7)),
				'14 days' => __d('core', '%s days', array(14)),
				'30 days' => __d('core', '%s days', array(30)),
				'60 days' => __d('core', '%s days', array(60)),
				'90 days' => __d('core', '%s days', array(90)),
				'180 days' => __d('core', '%s days', array(180)),
				'365 days' => __d('core', '%s days', array(365)),
				'999 days' => __d('core', '%s days', array(999)),
			)));
			?>
		</div>
	</div>
	<div class="form-actions-bar">
		<?php
		echo $this->Form->button(__d('core', 'Save'), array('div' => false, 'class' => 'button green primary'));
		echo $this->CHtml->backendLink(__d('core', 'Reset'), '/settings/edit', array('class' => 'button danger'));
		?>
	</div>
	<?php echo $this->Form->end(); ?>
</div>