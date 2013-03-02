<div class="round-shadow">
	<div class="title-pad">
		<h1><?php echo __d('core', 'All Languages') ?></h1>
		<ul class="actions">
			<li><?php echo $this->Html->link(__d('core', 'Add a new Language'), "/${backend_prefix}/languages/add", array('class' => 'add', 'title' => __d('core', 'Add a new Language'))) ?></li>
		</ul>
	</div>
	<table class="list bottom-round">
		<thead>
		<tr>
			<th class="g1 center">ID</th>
			<th class="g4"><?php echo __d('core', 'Language') ?></th>
			<th class="g2"><?php echo __d('core', 'Locale') ?></th>
			<th class="g2"><?php echo __d('core', 'ISO') ?></th>
			<th class="g2"><?php echo __d('core', 'lang') ?></th>
			<th class="g3"><?php echo __d('core', 'Availability') ?></th>
			<th class="g2 center"><?php echo __d('core', 'Actions') ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		$i = 1;
		foreach($languages as $lang) {
			$class = ($i % 2 == 0) ? ' class="even"' : '';
			?>
			<tr<?php echo $class ?>>
				<td class="right"><?php echo $lang['Language']['id'] ?></td>
				<td><?php echo $this->Html->link($lang['Language']['name'], "/${backend_prefix}/languages/edit/" . $lang['Language']['id'], array('title' => __d('core', 'Edit this Language'))) ?></td>
				<td><?php echo $lang['Language']['locale'] ?></td>
				<td><?php echo $lang['Language']['iso'] ?></td>
				<td><?php echo $lang['Language']['lang'] ?></td>
				<td>
					<?php
					$avail_class = '';
					if ($lang['Language']['available_at_frontend'] === true) {
						$avail_class = ' label-info';
					}
					?>
					<span class="label<?php echo $avail_class; ?>">Frontend</span>
					<?php
					$avail_class = '';
					if ($lang['Language']['available_at_backend'] === true) {
						$avail_class = ' label-info';
					}
					?>
					<span class="label<?php echo $avail_class; ?>">Backend</span>
				</td>
				<td class="actions center">
					<?php
					if (!in_array($lang['Language']['id'], array(1, 2))) {
						echo $this->Html->link(__d('core', 'Delete this Language'), '#', array('title' => __d('core', 'Delete this Language'), 'class' => 'remove confirm', 'data-confirm' => __d('core', 'Delete language <strong>%s</strong> ?', array($lang['Language']['name'])), 'data-confirm-action' => Router::url("/${backend_prefix}/languages/delete/" . $lang['Language']['id']), 'data-modal-title' => __d('core', 'Deletion Confirmation')));
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