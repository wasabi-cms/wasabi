<?php
/**
 * @var CoreView $this
 * @var array $group
 */

$this->CHtml->setTitle(__d('core', 'Move existing Member(s)'));
$this->CHtml->setSubTitle($group['Group']['name']);

echo $this->Form->create('Group');
echo $this->CForm->input('alternative_group_id', array('label' => __d('core', 'Group').':', 'options' => $groups, 'info' => __d('core', 'Please select a group where the existing member(s) should be moved to.')));
?>
<div class="form-controls">
	<?php
	echo $this->Form->button('<span>' . __d('core', 'Move Members & Delete Group') . '</span>', array('div' => false, 'class' => 'button'));
	echo $this->CHtml->backendLink(__d('core', 'Cancel'), '/groups');
	?>
</div>
<?php echo $this->Form->end(); ?>