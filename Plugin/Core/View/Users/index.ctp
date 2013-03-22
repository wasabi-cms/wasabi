<?php /** @var CoreView $this */ ?>
<div class="round-shadow">
	<div class="title-pad">
		<h1><?php echo __d('core', 'All Users') ?></h1>
		<ul class="actions">
			<li><?php echo $this->Html->link(__d('core', 'Add a new User'), "/${backend_prefix}/users/add", array('class' => 'add', 'title' => __d('core', 'Add a new User'))) ?></li>
		</ul>
	</div>
	<table class="list bottom-round">
		<thead>
		<tr>
			<th class="g1 center">ID</th>
			<th class="g4"><?php echo __d('core', 'User') ?></th>
			<th class="g4"><?php echo __d('core', 'Group') ?></th>
			<th class="g4"><?php echo __d('core', 'Status') ?></th>
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
				<td>
					<?php
					if ($u['User']['id'] == 1 && Authenticator::get('User.id') != 1) {
						echo '<strong>' . $u['User']['username'] . '</strong>';
					} else {
						echo $this->Html->link($u['User']['username'], "/${backend_prefix}/users/edit/" . $u['User']['id'], array('title' => __d('core', 'Edit this User')));
					}
					?>
				</td>
				<td><?php echo $u['Group']['name'] ?></td>
				<td>
					<?php
					$avail_class = '';
					$status_text = 'inactive';
					if ($u['User']['active'] === true) {
						$avail_class = ' label-info';
						$status_text = 'active';
					}
					?>
					<span class="label<?php echo $avail_class; ?>"><?php echo $status_text ?></span>
				</td>
				<td class="actions center">
					<?php
					if ($u['User']['id'] != Authenticator::get('id') && $u['User']['id'] != 1) {
						echo $this->Html->link(__d('core', 'Delete this User'), '#', array('title' => __d('core', 'Delete this User'), 'class' => 'remove confirm', 'data-confirm' => __d('core', 'Delete user <strong>%s</strong> ?', array($u['User']['username'])), 'data-confirm-action' => Router::url("/${backend_prefix}/users/delete/" . $u['User']['id']), 'data-modal-title' => __d('core', 'Deletion Confirmation')));
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