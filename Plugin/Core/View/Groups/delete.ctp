<?php
/**
 * @var CoreView $this
 * @var array $group
 */

$this->CHtml->setTitle(__d('core', 'Delete Group'));
$this->CHtml->setSubTitle($group['Group']['name']);

echo $this->Form->create('Group');
?>
<div class="box info-box">
	<div class="box-title row"></div>
	<div class="box-content">
		<p><?php echo __d('core', 'The group <strong>%s</strong> still has <strong>%s</strong> member(s).', array($group['Group']['name'], $group['Group']['user_count'])) ?></p>
		<p><?php echo __d('core', 'Please select a group below where the existing member(s) should be moved to.') ?></p>
	</div>
</div>
<div class="box">
	<div class="box-title row"><i class="icon-edit"></i><h3><?php echo __d('core', 'New Group for existing Users') ?></h3></div>
	<div class="box-content">
		<?php
		echo $this->CForm->input('alternative_group_id', array('label' => __d('core', 'Group').':', 'options' => $groups));
		?>
	</div>
</div>
<div class="form-controls fixed">
	<?php
	echo $this->Form->button(__d('core', 'Move Members & Delete Group'), array('div' => false, 'class' => 'button green primary'));
	echo $this->CHtml->backendLink(__d('core', 'Cancel'), '/groups', array('class' => 'button danger'));
	?>
</div>
<?php echo $this->Form->end(); ?>