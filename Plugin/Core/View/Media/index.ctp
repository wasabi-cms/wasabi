<?php
/**
 * @var CoreView $this
 * @var array $media
 * @var string $author
 * @var array $typeCounts
 */

$this->Html->setTitle(__d('core', 'Media'));
$this->Html->addAction(
	$this->Html->backendLink('<i class="icon-plus"></i>', '/media/add', array('class' => 'add', 'title' => __d('core', 'Add Media'), 'escape' => false))
);
$this->Bulk->setTarget('media[]');
$this->Bulk->addActions(array(
	Router::url(array('action' => 'deleteBulk')) => __d('core', 'Delete Permanently')
));

?>
<div class="filters row">
	<div class="span8">
		<?php echo $this->Filter->activeFilters(array(
			'author' => __d('core', 'Author: <strong>%s</strong>', array($author)),
			'file' => __d('core', 'File: {{VALUE}}')
		)); ?>
	</div>
	<div class="span8">
		<?php echo $this->Form->input('file', array('label' => false)); ?>
	</div>
</div>
<div class="row">
	<?php #echo $this->Bulk->render('top'); ?>
</div>
<?php echo $this->Form->create('Media', array('url' => array('action' => 'bulk'))); ?>
<?php echo $this->Filter->groups(array(
	'EMPTY' => array(
		'name' => __d('core', 'All'),
		'title' => __d('core', 'Show all media'),
		'count' => $typeCounts['all']
	),
	'type' => array(
		'image' => array(
			'name' => __d('core', 'Images'),
			'title' => __d('core', 'Only show images'),
			'count' => isset($typeCounts['image']) ? $typeCounts['image'] : 0
		),
		'video' => array(
			'name' => __d('core', 'Videos'),
			'title' => __d('core', 'Only show videos'),
			'count' => isset($typeCounts['video']) ? $typeCounts['video'] : 0
		),
		'audio' => array(
			'name' => __d('core', 'Audio'),
			'title' => __d('core', 'Only show audio files'),
			'count' => isset($typeCounts['audio']) ? $typeCounts['audio'] : 0
		),
		'document' => array(
			'name' => __d('core', 'Documents'),
			'title' => __d('core', 'Only show documents'),
			'count' => isset($typeCounts['document']) ? $typeCounts['document'] : 0
		),
		'other' => array(
			'name' => __d('core', 'Other'),
			'title' => __d('core', 'Only show other files'),
			'count' => isset($typeCounts['other']) ? $typeCounts['other'] : 0
		),
	),
	'detached' => array(
		'1' => array(
			'name' => __d('core', 'Unattached'),
			'title' => __d('core', 'Only show unattached media'),
			'count' => 7
		)
	)
)); ?>
<table class="list valign-middle">
	<thead>
	<tr>
		<th class="t35px tselect"><input type="checkbox" data-toggle="select" data-target="media[]"/></th>
		<th class="t80px"><?php echo 'ID'; ?></th>
		<th class="t80px"></th>
		<th class="t7"><?php echo $this->Filter->sortLink(__d('core', 'File'), 'file') ?></th>
		<th class="t2"><?php echo $this->Filter->sortLink(__d('core', 'Author'), 'author') ?></th>
		<th class="t2"><?php echo __d('core', 'Attached To') ?></th>
		<th class="t130px"><?php echo $this->Filter->sortLink(__d('core', 'Date'), 'date') ?></th>
		<th class="t1 center"><?php echo __d('core', 'Actions') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 1;
	foreach($media as $m) {
		$class = ($i % 2 == 0) ? ' class="even"' : '';
		if (CakeTime::isToday($m['Media']['created'])) {
			$date = __d('core', 'Today') . ', ' . date('H:i', strtotime($m['Media']['created']));
		} else if (CakeTime::wasYesterday($m['Media']['created'])) {
			$date = __d('core', 'Yesterday') . ', ' . date('H:i', strtotime($m['Media']['created']));
		} else {
			$date = date('d.m.Y', strtotime($m['Media']['created']));
		}
		?>
		<tr<?php echo $class ?>>
			<td class="tselect"><input type="checkbox" name="media[]" value="<?php echo $m['Media']['id'] ?>"/></td>
			<td><?php echo $m['Media']['id'] ?></td>
			<td><?php
				$link = preg_replace('/\\\/', '/', Router::url('/'. $m['Media']['upload_path']));
				$class = '';
				$descTop = false;
				$desc = '';
				switch ($m['Media']['type']) {
					case 'image':
						if ($m['Media']['mime_type'] !== 'image/svg+xml') {
							$descTop = $this->Image->resize($m['Media'], array(
								'resize_method' => 'crop',
								'width' => 60,
								'height' => 60
							));
						} else {
							$class = 'other';
							$desc = strtoupper($m['Media']['ext']);
						}
						break;
					case 'video':
						$class = 'video';
						$descTop = '<span class="play"><i class="icon-play"></i></span>';
						$desc = '<i class="icon-film"></i> ' . strtoupper($m['Media']['ext']);
						break;
					case 'audio':
						$class = 'audio';
						$descTop = '<span class="play"><i class="icon-play"></i></span>';
						$desc = '<i class="icon-music"></i> ' . strtoupper($m['Media']['ext']);
						break;
					default:
						switch ($m['Media']['mime_type']) {
							case 'application/pdf':
								$class = 'pdf';
								$desc = strtoupper($m['Media']['ext']);
								break;
							case 'application/vnd.ms-excel':
							case 'application/vnd.ms-excel.sheet.macroEnabled.12':
							case 'application/vnd.ms-excel.template.macroEnabled.12':
							case 'application/vnd.ms-excel.addin.macroEnabled.12':
							case 'application/vnd.ms-excel.sheet.binary.macroEnabled.12':
							case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
							case 'application/vnd.openxmlformats-officedocument.spreadsheetml.template':
								$class = 'xls';
								$desc = strtoupper($m['Media']['ext']);
								break;
							case 'application/vnd.ms-powerpoint':
							case 'application/vnd.ms-powerpoint.addin.macroEnabled.12':
							case 'application/vnd.ms-powerpoint.presentation.macroEnabled.12':
							case 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12':
							case 'application/vnd.openxmlformats-officedocument.presentationml.template':
							case 'application/vnd.openxmlformats-officedocument.presentationml.slideshow':
							case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
								$class = 'ppt';
								$desc = strtoupper($m['Media']['ext']);
								break;
							default:
								$class = 'other';
								$desc = strtoupper($m['Media']['ext']);
								break;
						}
						break;
				}
				echo '<a href="' . $link . '" class="' . $class . '" target="_blank">'
					. (($descTop !== false) ? $descTop : '')
					. (($desc !== '') ? '<span class="desc">' . $desc . '</span>' : '')
					. '</a>';
			?></td>
			<td><?php echo $this->Html->backendLink($m['Media']['fullname'], '/media/edit/' . $m['Media']['id']) ?></td>
			<td><?php echo $this->Filter->link($m['User']['username'], array('author' => $m['User']['id']), false, array('title' => __d('core', 'Show all media uploaded by %s', array($m['User']['username'])))) ?></td>
			<td><?php echo '(unattached)' ?></td>
			<td><?php echo $date ?></td>
			<td class="actions center">
				<?php
				echo $this->Html->backendConfirmationLink(__d('core', 'delete'), '/media/delete/' . $m['Media']['id'], array(
					'class' => 'wicon-remove',
					'title' => __d('core', 'Delete this File'),
					'confirm-message' => __d('core', 'Delete file <strong>%s</strong> ?', array($m['Media']['fullname'])),
					'confirm-title' => __d('core', 'Deletion Confirmation')
				));
				?>
			</td>
		</tr>
		<?php
		$i++;
	}
	?>
	</tbody>
	<tfoot>
	<tr>
		<th class="tselect"><input type="checkbox" data-toggle="select-secondary" data-target="media[]"/></th>
		<th><?php echo 'ID'; ?></th>
		<th></th>
		<th><?php echo $this->Filter->sortLink(__d('core', 'File'), 'file') ?></th>
		<th><?php echo $this->Filter->sortLink(__d('core', 'Author'), 'author') ?></th>
		<th><?php echo __d('core', 'Attached To') ?></th>
		<th><?php echo $this->Filter->sortLink(__d('core', 'Date'), 'date') ?></th>
		<th class="center"><?php echo __d('core', 'Actions') ?></th>
	</tr>
	</tfoot>
</table>
<?php echo $this->Form->end(); ?>
<div class="row">
	<div class="span8">
		<?php echo $this->Bulk->render('bottom'); ?>
	</div>
	<div class="span8">
		<?php echo $this->Filter->pagination() ?>
	</div>
</div>
