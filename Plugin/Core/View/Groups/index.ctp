<?php
/**
 * @var CoreView $this
 * @var array $groups
 */

$this->CHtml->setTitle(__d('core', 'Groups'));

$this->CHtml->addAction(
	$this->CHtml->backendLink('<i class="icon-plus"></i>', '/groups/add', array('class' => 'add', 'title' => __d('core', 'Add a new Group'), 'escape' => false))
);
?>
<table class="list valign-middle">
	<thead>
	<tr>
		<th class="t1 center">ID</th>
		<th class="t6"><?php echo __d('core', 'Group') ?></th>
		<th class="t6"><?php echo __d('core', '# Users') ?></th>
		<th class="t3 center"><?php echo __d('core', 'Actions') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 1;
	foreach($groups as $g) {
		$class = ($i % 2 == 0) ? ' class="even"' : '';
		?>
		<tr<?php echo $class ?>>
			<td class="right"><?php echo $g['Group']['id'] ?></td>
			<td><?php echo $this->CHtml->backendLink($g['Group']['name'], '/groups/edit/' . $g['Group']['id'], array('title' => __d('core', 'Edit this Group'))) ?></td>
			<td><?php echo $g['Group']['user_count'] ?></td>
			<td class="actions center">
				<?php
				if ($g['Group']['id'] != 1) {
					echo $this->CHtml->backendConfirmationLink(__d('core', 'delete'), '/groups/delete/' . $g['Group']['id'], array(
						'title' => __d('core', 'Delete this Group'),
						'class' => 'wicon-remove',
						'confirm-message' => __d('core', 'Delete the group <strong>%s</strong> ?', array($g['Group']['name'])),
						'confirm-title' => __d('core', 'Deletion Confirmation')
					));
				} else {
					echo '-';
				}
				?>
			</td>
		</tr>
		<?php
		$i++;
	}
	?>
	</tbody>
</table>