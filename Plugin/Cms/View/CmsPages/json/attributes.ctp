<?php
ob_start();
echo $this->element('Cms.cms_page_layout_attributes');
$out = ob_get_clean();

echo json_encode($out);
