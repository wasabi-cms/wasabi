<?php
/**
 * @var CoreView $this
 */

if ($this->params['action'] === 'add') {
	$this->CHtml->setTitle(__d('core', 'Add a new Group'));
} else {
	$this->CHtml->setTitle(__d('core', 'Edit Group'));
	$this->CHtml->setSubTitle($this->data['Group']['name']);
}

echo $this->Form->create('Group');
if ($this->params['action'] == 'edit') {
	echo $this->Form->input('id', array('type' => 'hidden'));
}

echo $this->CForm->input('name', array('label' => __d('core', 'Group Name')));
?>
<div class="form-controls">
	<?php
	echo $this->Form->button('<span>' . __d('core', 'Save') . '</span>', array('div' => false, 'class' => 'button'));
	echo $this->CHtml->backendLink(__d('core', 'Cancel'), '/groups');
	?>
</div>
<?php echo $this->Form->end(); ?>