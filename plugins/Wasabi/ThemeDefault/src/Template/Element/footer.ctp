<?php
/**
 * @var \Wasabi\ThemeDefault\View\ThemeDefaultView $this
 */

$this->start('bottom_body');
$this->end();
echo $this->fetch('bottom_body');

$this->start('bottom_js');
$this->end();
echo $this->fetch('bottom_js');
?>
</body>
</html>
