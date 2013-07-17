<?php
/**
 * @var CoreView $this
 */
?>
<?php if (!isset($sql_dump) || $sql_dump !== false): ?>
<div class="row">
	<?php echo $this->element('sql_dump'); ?>
</div>
<?php endif; ?>