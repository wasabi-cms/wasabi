<?php
/**
 * @var integer $id The page id to be edited.
 */
?>
<iframe id="live-edit-iframe" seamless data-src="<?php echo Router::url(array('plugin' => 'cms', 'controller' => 'cms_pages', 'action' => 'live', $id)) ?>" frameborder="0"></iframe>