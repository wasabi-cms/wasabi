<div class="round-shadow">
	<div class="title-pad">
		<h1><?php echo __d('core', 'All Users') ?></h1>
		<ul class="actions">
			<li><?php echo $this->Html->link(__d('core', 'Add a new User'), "/${backend_prefix}/users/add", array('class' => 'add', 'title' => __d('core', 'Add a new User'))) ?></li>
		</ul>
	</div>
	<table class="list">
		<thead>
		<tr>
			<th class="g1 center">ID</th>
			<th class="g6"><?php echo __d('core', 'User') ?></th>
			<th class="g6"><?php echo __d('core', 'Group') ?></th>
			<th class="g3 center"><?php echo __d('core', 'Actions') ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		$i = 1;
		foreach($users as $u) {
			$class = ($i % 2 == 0) ? ' class="even"' : '';
			?>
			<tr<?php echo $class ?>>
				<td class="right"><?php echo $u['User']['id'] ?></td>
				<td><?php echo $this->Html->link($u['User']['username'], "/${backend_prefix}/users/edit/" . $u['User']['id'], array('title' => __d('core', 'Edit this User'))) ?></td>
				<td><?php echo $u['Group']['name'] ?></td>
				<td class="actions center">
					<?php if ($u['User']['id'] != Configure::read('User.id')): ?>
						<?php echo $this->Html->link(__d('core', 'Delete this User'), "/${backend_prefix}/users/delete/" . $u['User']['id'], array('title' => __d('core', 'Delete this User'), 'class' => 'remove confirm', 'data-confirm' => __d('core', 'Do you really want to delete this User?'))) ?>
					<?php else: echo '-'; endif; ?>
				</td>
			</tr>
			<?php
			$i++;
		}
		?>
		</tbody>
	</table>
</div>