<?php
/**
 * @var CmsPageView $this
 */

$this->start('bottom_body');
echo $this->element('sql_dump');
$this->end('bottom_body');
echo $this->fetch('bottom_body');
//echo $this->WasabiAsset->js('/js/jquery-1.10.2.min.js');
//echo $this->WasabiAsset->js('/js/plugins.js', 'Core');
//echo $this->WasabiAsset->js('/js/script.js', 'Core');
$this->start('bottom_js');
$this->end('bottom_js');
echo $this->fetch('bottom_js');
?>
</body>
</html>