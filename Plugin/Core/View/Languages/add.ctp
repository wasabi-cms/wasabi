<?php
/**
 * @var CoreView $this
 */
?>
<div class="c12">
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
				echo $this->CForm->input('in_progress', array('label' => __d('core', 'in progress'), 'type' => 'checkbox', 'title' => __d('core', 'Progress').':'));
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
</div>
<div class="c4">
	<h3>Language FAQ</h3>
	<p>Languages are used for both, the backend you are currently looking at and the frontend of your website.</p>
	<p>You can individually decide if a language should be available at the frontend and/or backend.</p>
	<h4>Working on a new Language</h4>
	<p>If your website is already live and you want to add a new language, then you can select <strong>available at Frontend</strong> and mark it as <strong>in progress</strong>. This way the language will available for editing, but not be displayed at the frontend until you decide to do so.</p>
</div>