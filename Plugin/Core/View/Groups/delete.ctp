<?php
/**
 * @var CoreView $this
 * @var array $group
 * @var array $groups
 */

$this->Html->setTitle(__d('core', 'Move existing Member(s)'));
$this->Html->setSubTitle($group['Group']['name']);

echo $this->Form->create('Group', array('class' => 'no-top-section'));
echo $this->CForm->input('alternative_group_id', array('label' => __d('core', 'Group').':', 'options' => $groups, 'info' => __d('core', 'Please select a group where the existing member(s) should be moved to.')));
?>
<div class="form-controls">
	<?php
	echo $this->Form->button(__d('core', 'Move Members & Delete Group'), array('div' => false, 'class' => 'button red'));
	echo $this->Html->backendLink(__d('core', 'Cancel'), '/groups');
	?>
</div>
<?php echo $this->Form->end(); ?>