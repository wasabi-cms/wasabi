<div class="round-shadow">
	<div class="title-pad">
		<h1><?php echo __d('core', 'All Groups') ?></h1>
		<ul class="actions">
			<li><?php echo $this->Html->link(__d('core', 'Add a new Group'), "/${backend_prefix}/groups/add", array('class' => 'add', 'title' => __d('core', 'Add a new Group'))) ?></li>
		</ul>
	</div>
	<table class="list bottom-round">
		<thead>
		<tr>
			<th class="g1 center">ID</th>
			<th class="g6"><?php echo __d('core', 'Group') ?></th>
			<th class="g6"><?php echo __d('core', '# Users') ?></th>
			<th class="g3 center"><?php echo __d('core', 'Actions') ?></th>
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
				<td><?php echo $this->Html->link($g['Group']['name'], "/${backend_prefix}/groups/edit/" . $g['Group']['id'], array('title' => __d('core', 'Edit this Group'))) ?></td>
				<td><?php echo $g['Group']['user_count'] ?></td>
				<td class="actions center">
					<?php
					if ($g['Group']['id'] != 1) {
						echo $this->Html->link(__d('core', 'Delete this Group'), '#', array('title' => __d('core', 'Delete this Group'), 'class' => 'remove confirm', 'data-confirm' => __d('core', 'Delete the group <strong>%s</strong> ?', array($g['Group']['name'])), 'data-confirm-action' => Router::url("/${backend_prefix}/groups/delete/" . $g['Group']['id']), 'data-modal-title' => __d('core', 'Deletion Confirmation')));
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
</div>