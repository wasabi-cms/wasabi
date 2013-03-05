<div class="round-shadow">
	<div class="title-pad">
		<h1><?php echo __d('core', 'All Plugins') ?></h1>
		<ul class="actions">
			<li><?php echo $this->Html->link(__d('core', 'Find new Plugins'), "/${backend_prefix}/plugins/update", array('class' => 'button icon reload', 'title' => __d('core', 'Find new Plugins'))) ?></li>
		</ul>
	</div>
	<table class="list bottom-round">
		<thead>
		<tr>
			<th class="g1 center">ID</th>
			<th class="g4"><?php echo __d('core', 'Plugin') ?></th>
			<th class="g4"><?php echo __d('core', 'Status') ?></th>
			<th class="g4"><?php echo __d('core', 'Actions') ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		$i = 1;
		foreach($plugins as $p) {
			$class = ($i % 2 == 0) ? ' class="even"' : '';
			?>
			<tr<?php echo $class ?>>
				<td class="right"><?php echo $p['Plugin']['id'] ?></td>
				<td><?php echo $p['Plugin']['name'] ?></td>
				<td>
					<?php if ($p['Plugin']['flagged'] === true) { ?>
						<span class="label label-error">not correctly uninstalled</span>
					<?php } else { ?>
					<?php
					$class = '';
					$text = __d('core', 'inactive');
					if ($p['Plugin']['active'] === true) {
						$class = ' label-info';
						$text = __d('core', 'active');
					}
					echo '<span class="label' . $class . '">' . $text . '</span>';
					?>
					<?php
					$class = '';
					$text = __d('core', 'not installed');
					if ($p['Plugin']['installed'] === true) {
						$class = ' label-info';
						$text = 'installed';
					}
					echo '<span class="label' . $class . '">' . $text . '</span>';
					?>
					<?php } ?>
				</td>
				<td>
					<?php
					if (!$p['Plugin']['flagged']) {
						if (!$p['Plugin']['installed']) {
							echo $this->Html->link(__d('core', 'install'), '#', array(
								'title' => __d('core', 'Install this Plugin'),
								'class' => 'confirm',
								'data-confirm' => __d('core', 'Install <strong>%s</strong> plugin?', array($p['Plugin']['name'])),
								'data-confirm-action' => Router::url("/${backend_prefix}/plugins/install/" . $p['Plugin']['id']),
								'data-modal-title' => __d('core', 'Confirm Installation')
							));
						} else {
							if (!$p['Plugin']['active']) {
								echo $this->Html->link(__d('core', 'activate'), '#', array(
									'title' => __d('core', 'Activate this Plugin'),
									'class' => 'confirm',
									'data-confirm' => __d('core', 'Activate <strong>%s</strong> plugin?', array($p['Plugin']['name'])),
									'data-confirm-action' => Router::url("/${backend_prefix}/plugins/activate/" . $p['Plugin']['id']),
									'data-modal-title' => __d('core', 'Confirm Activation')
								));
								echo $this->Html->link(__d('core', 'uninstall'), '#', array(
									'title' => __d('core', 'Uninstall this Plugin'),
									'class' => 'confirm',
									'data-confirm' => __d('core', 'Uninstall <strong>%s</strong> plugin?', array($p['Plugin']['name'])),
									'data-confirm-action' => Router::url("/${backend_prefix}/plugins/uninstall/" . $p['Plugin']['id']),
									'data-modal-title' => __d('core', 'Confirm Uninstallation')
								));
							} else {
								echo $this->Html->link(__d('core', 'deactivate'), '#', array(
									'title' => __d('core', 'Deactivate this Plugin'),
									'class' => 'confirm',
									'data-confirm' => __d('core', 'Deactivate <strong>%s</strong> plugin?', array($p['Plugin']['name'])),
									'data-confirm-action' => Router::url("/${backend_prefix}/plugins/deactivate/" . $p['Plugin']['id']),
									'data-modal-title' => __d('core', 'Confirm Deactivation')
								));
							}
						}
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