<?php
/**
 * @var CoreView $this
 * @var array $plugins
 */

$this->CHtml->setTitle(__d('core', 'Plugins'));
?>
<table class="list">
	<thead>
	<tr>
		<th class="t3"><?php echo __d('core', 'Plugin') ?></th>
		<th class="t4"><?php echo __d('core', 'Description') ?></th>
		<th class="t4"><?php echo __d('core', 'Author') ?></th>
		<th class="t3"><?php echo __d('core', 'Status') ?></th>
		<th class="t2"><?php echo __d('core', 'Actions') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php if (empty($plugins)): ?>
		<tr><td class="center warn" colspan="5"><?php echo __d('core', 'There are no plugins available.') ?></td></tr>
	<?php endif; ?>
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
					if (isset($p['PluginInfo']['authorEmail'])) {
						echo $this->Html->link($p['PluginInfo']['author'], 'mailto://'.$p['PluginInfo']['authorEmail']);
					} else {
						echo $p['PluginInfo']['author'];
					}
					if (isset($p['PluginInfo']['authorUrl'])) {
						echo '<br>';
						echo $this->Html->link($p['PluginInfo']['authorUrl'], $p['PluginInfo']['authorUrl'], array('target' => '_blank'));
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
					echo $this->CHtml->backendConfirmationLink(__d('core', 'install'), '/plugins/install/' . $p['Plugin']['name'], array(
						'title' => __d('core', 'Install this Plugin'),
						'confirm-message' => __d('core', 'Install <strong>%s</strong> plugin?', array($p['Plugin']['name'])),
						'confirm-title' => __d('core', 'Confirm Installation')
					));
				} else {
					if (!$p['Plugin']['active']) {
						echo $this->CHtml->backendConfirmationLink(__d('core', 'activate'), '/plugins/activate/' . $p['Plugin']['name'], array(
							'title' => __d('core', 'Activate this Plugin'),
							'confirm-message' => __d('core', 'Activate <strong>%s</strong> plugin?', array($p['Plugin']['name'])),
							'confirm-title' => __d('core', 'Confirm Activation')
						));
						echo '<br>';
						echo $this->CHtml->backendConfirmationLink(__d('core', 'uninstall'), '/plugins/uninstall/' . $p['Plugin']['name'], array(
							'title' => __d('core', 'Uninstall this Plugin'),
							'confirm-message' => __d('core', 'Uninstall <strong>%s</strong> plugin?', array($p['Plugin']['name'])),
							'confirm-title' => __d('core', 'Confirm Uninstallation')
						));
					} else {
						echo $this->CHtml->backendConfirmationLink(__d('core', 'deactivate'), '/plugins/deactivate/' . $p['Plugin']['name'], array(
							'title' => __d('core', 'Deactivate this Plugin'),
							'confirm-message' => __d('core', 'Deactivate <strong>%s</strong> plugin?', array($p['Plugin']['name'])),
							'confirm-title' => __d('core', 'Confirm Deactivation')
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