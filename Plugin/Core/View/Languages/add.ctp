<?php
/**
 * @var CoreView $this
 */
?>
<div class="round-shadow">
	<div class="title-pad">
		<h1><?php echo ($this->params['action'] == 'add') ? __d('core', 'Add a new Language') : __d('core', 'Edit Language <strong>%s</strong>', array($this->data['Language']['name'])) ?></h1>
	</div>
	<?php echo $this->Form->create('Language'); ?>
	<div class="page-content form-content">
		<?php
		if ($this->params['action'] == 'edit') {
			echo $this->Form->input('id', array('type' => 'hidden'));
		}
		?>
		<div class="form-row-wrapper">
			<?php
			echo $this->CForm->input('name', array('label' => __d('core', 'Language Name').':'));
			echo $this->CForm->input('locale', array('label' => __d('core', 'Locale').':', 'info' => __d('core', 'The 2 char short code of the language (e.g. en, de).')));
			echo $this->CForm->input('iso', array('label' => __d('core', 'ISO').':', 'info' => __d('core', 'The 3 char ISO code of the language (e.g. eng, deu).')));
			echo $this->CForm->input('lang', array('label' => __d('core', 'HTML Lang Code').':', 'info' => __d('core', 'The html specific lang code (e.g. en-US, de-DE).')));
			echo $this->CForm->input('available_at_frontend', array('label' => __d('core', 'available at Frontend'), 'type' => 'checkbox', 'title' => __d('core', 'Frontend').':'));
			echo $this->CForm->input('available_at_backend', array('label' => __d('core', 'available at Backend'), 'type' => 'checkbox', 'title' => __d('core', 'Backend').':'));
			?>
		</div>
	</div>
	<div class="form-actions-bar">
		<?php
		echo $this->Form->button(__d('core', 'Save'), array('div' => false, 'class' => 'button green primary'));
		echo $this->CHtml->backendLink(__d('core', 'Cancel'), '/languages', array('class' => 'button danger'));
		?>
	</div>
	<?php echo $this->Form->end(); ?>
</div>