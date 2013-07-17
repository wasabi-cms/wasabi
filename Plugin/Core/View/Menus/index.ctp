<?php
/**
 * @var CoreView $this
 * @var array $menus
 */

$this->CHtml->setTitle(__d('core', 'Menus'));
$this->CHtml->addAction(
	$this->CHtml->backendLink('<i class="icon-plus"></i>', 'menus/add', array('class' => 'add', 'title' => __d('core', 'Add a new Menu'), 'escape' => false))
);
?>
<table class="list">
	<thead>
	<tr>
		<th class="t1 center">ID</th>
		<th class="t6"><?php echo __d('core', 'Menu Name') ?></th>
		<th class="t6"><?php echo __d('core', '# Menu Items') ?></th>
		<th class="t3 center"><?php echo __d('core', 'Actions') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 1;
	foreach($menus as $m) {
		$class = ($i % 2 == 0) ? ' class="even"' : '';
		?>
		<tr<?php echo $class ?>>
			<td class="right"><?php echo $m['Menu']['id'] ?></td>
			<td><?php echo $this->CHtml->backendLink($m['Menu']['name'], 'menus/edit/' . $m['Menu']['id'], array('title' => __d('core', 'Edit this Menu'))) ?></td>
			<td><?php echo $m['Menu']['menu_item_count'] ?></td>
			<td class="actions center">
				<?php
				echo $this->CHtml->backendConfirmationLink('<i class="icon-remove"></i>', 'menus/delete/' . $m['Menu']['id'], array(
					'title' => __d('core', 'Delete this Menu'),
					'class' => 'remove',
					'confirm-message' => __d('core', 'Do you really want to delete menu <strong>%s</strong> ?', array($m['Menu']['name'])),
					'confirm-title' => __d('core', 'Deletion Confirmation'),
					'escape' => false
				));
				?>
			</td>
		</tr>
		<?php
		$i++;
	}
	?>
	</tbody>
</table>