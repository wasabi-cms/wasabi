<div class="round-shadow">
	<div class="title-pad">
		<h1><?php echo __d('core', 'Delete Group <strong>%s</strong>', array($group['Group']['name'])) ?></h1>
	</div>
	<div class="page-content form-content">
		<?php
		echo $this->Form->create('Group');
		?>
		<div class="infobox">
			<?php
			echo '<p>' . __d('core', 'The group <strong>%s</strong> still has <strong>%s</strong> member(s).', array($group['Group']['name'], $group['Group']['user_count'])) . '</p>';
			echo '<p>' . __d('core', 'Please select a group below where the existing member(s) should be moved to.') . '</p>';
			?>
		</div>
		<div class="form-row-wrapper">
			<?php
			echo $this->CForm->input('alternative_group_id', array('label' => __d('core', 'Group').':', 'options' => $groups));
			?>
		</div>
	</div>
	<div class="form-actions-bar">
		<?php
		echo $this->Form->button(__d('core', 'Move Members & Delete Group'), array('div' => false, 'class' => 'button green primary'));
		echo $this->Html->link(__d('core', 'Cancel'), "/${backend_prefix}/groups", array('class' => 'button danger'));
		echo $this->Form->end();
		?>
	</div>
</div>