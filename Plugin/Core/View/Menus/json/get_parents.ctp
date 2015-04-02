<?php
/**
 * @var array $parentItems
 * @var boolean|integer $setMenuId
 */
ob_start();
$options = array(
	'label' => false,
	'div' => false,
	'options' => $parentItems,
	'empty' => __d('core', '-- None --')
);
if ($setMenuId !== false) {
	$options['value'] = $setMenuId;
}
echo $this->Form->input('Media.parent_id', $options);
$out = ob_get_clean();

echo json_encode($out);