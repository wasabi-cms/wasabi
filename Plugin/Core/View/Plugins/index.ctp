<div class="round-shadow">
	<div class="title-pad">
		<h1><?php echo __d('core', 'All Plugins') ?></h1>
	</div>
	<table class="list bottom-round">
		<thead>
		<tr>
			<th class="g3"><?php echo __d('core', 'Plugin') ?></th>
			<th class="g4"><?php echo __d('core', 'Description') ?></th>
			<th class="g4"><?php echo __d('core', 'Author') ?></th>
			<th class="g3"><?php echo __d('core', 'Status') ?></th>
			<th class="g2"><?php echo __d('core', 'Actions') ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		$i = 1;
		foreach($plugins as $p) {
			$class = ($i % 2 == 0) ? ' class="even"' : '';
			?>
			<tr<?php echo $class ?>>
				<td>
					<?php
					if (isset($p['PluginInfo']['name'])) {
						echo $p['PluginInfo']['name'];
					} else {
						echo $p['Plugin']['name'];
					}
					if (isset($p['PluginInfo']['version'])) {
						echo ' (' . $p['PluginInfo']['version'] . ')';
					}
					?>
				</td>
				<td>
					<?php
					if (isset($p['PluginInfo']['description'])) {
						echo $p['PluginInfo']['description'];
					} else {
						echo '-';
					}
					?>
				</td>
				<td>
					<?php
					if (isset($p['PluginInfo']['author'])) {
						echo $p['PluginInfo']['author'];
						if (isset($p['PluginInfo']['authorUrl'])) {
							echo '<br>';
							echo $this->Html->link($p['PluginInfo']['authorUrl'], $p['PluginInfo']['authorUrl'], array('target' => '_blank'));
						}
						if (isset($p['PluginInfo']['authorEmail'])) {
							echo '<br>';
							echo $this->Html->link(__d('core', 'Support'), 'mailto://'.$p['PluginInfo']['authorEmail']);
						}
					} else {
						echo '-';
					}
					?>
				</td>
				<td>
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
				</td>
				<td>
					<?php
					if (!$p['Plugin']['installed']) {
						echo $this->Html->link(__d('core', 'install'), '#', array(
							'title' => __d('core', 'Install this Plugin'),
							'class' => 'confirm',
							'data-confirm' => __d('core', 'Install <strong>%s</strong> plugin?', array($p['Plugin']['name'])),
							'data-confirm-action' => Router::url("/${backend_prefix}/plugins/install/" . $p['Plugin']['name']),
							'data-modal-title' => __d('core', 'Confirm Installation')
						));
					} else {
						if (!$p['Plugin']['active']) {
							echo $this->Html->link(__d('core', 'activate'), '#', array(
								'title' => __d('core', 'Activate this Plugin'),
								'class' => 'confirm',
								'data-confirm' => __d('core', 'Activate <strong>%s</strong> plugin?', array($p['Plugin']['name'])),
								'data-confirm-action' => Router::url("/${backend_prefix}/plugins/activate/" . $p['Plugin']['name']),
								'data-modal-title' => __d('core', 'Confirm Activation')
							));
							echo '<br>';
							echo $this->Html->link(__d('core', 'uninstall'), '#', array(
								'title' => __d('core', 'Uninstall this Plugin'),
								'class' => 'confirm',
								'data-confirm' => __d('core', 'Uninstall <strong>%s</strong> plugin?', array($p['Plugin']['name'])),
								'data-confirm-action' => Router::url("/${backend_prefix}/plugins/uninstall/" . $p['Plugin']['name']),
								'data-modal-title' => __d('core', 'Confirm Uninstallation')
							));
						} else {
							echo $this->Html->link(__d('core', 'deactivate'), '#', array(
								'title' => __d('core', 'Deactivate this Plugin'),
								'class' => 'confirm',
								'data-confirm' => __d('core', 'Deactivate <strong>%s</strong> plugin?', array($p['Plugin']['name'])),
								'data-confirm-action' => Router::url("/${backend_prefix}/plugins/deactivate/" . $p['Plugin']['name']),
								'data-modal-title' => __d('core', 'Confirm Deactivation')
							));
						}
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