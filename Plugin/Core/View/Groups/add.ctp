<?php
/**
 * @var CoreView $this
 */

if ($this->params['action'] === 'add') {
	$this->Html->setTitle(__d('core', 'Add a new Group'));
} else {
	$this->Html->setTitle(__d('core', 'Edit Group'));
	$this->Html->setSubTitle($this->data['Group']['name']);
}

echo $this->Form->create('Group', array('class' => 'no-top-section'));
if ($this->params['action'] == 'edit') {
	echo $this->Form->input('id', array('type' => 'hidden'));
}

echo $this->CForm->input('name', array('label' => __d('core', 'Group Name')));
?>
<div class="form-controls">
	<?php
	echo $this->Form->button(__d('core', 'Save'), array('div' => false, 'class' => 'button'));
	echo $this->Html->backendLink(__d('core', 'Cancel'), '/groups');
	?>
</div>
<?php echo $this->Form->end(); ?>