<?php
ob_start();
echo $this->element('Cms.cms_page_routes');
$out = ob_get_clean();

echo json_encode($out);
