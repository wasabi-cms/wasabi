<?php /** @var CoreView $this */ ?>
<div class="round-shadow">
	<div class="title-pad">
		<h1><?php echo ($this->params['action'] == 'add') ? __d('core', 'Add a new Group') : __d('core', 'Edit Group <strong>%s</strong>', array($this->data['Group']['name'])) ?></h1>
	</div>
	<?php echo $this->Form->create('Group'); ?>
	<div class="page-content form-content">
		<?php
		if ($this->params['action'] == 'edit') {
			echo $this->Form->input('id', array('type' => 'hidden'));
		}
		?>
		<div class="form-row-wrapper">
			<?php
			echo $this->CForm->input('name', array('label' => __d('core', 'Group Name').':'));
			?>
		</div>
	</div>
	<div class="form-actions-bar">
		<?php
		echo $this->Form->button(__d('core', 'Save'), array('div' => false, 'class' => 'button green primary'));
		echo $this->CHtml->backendLink(__d('core', 'Cancel'), '/groups', array('class' => 'button danger'));
		?>
	</div>
	<?php echo $this->Form->end(); ?>
</div>