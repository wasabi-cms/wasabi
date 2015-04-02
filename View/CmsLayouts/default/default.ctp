<?php
/**
 * @var CmsPageView $this
 * @var string		$title_for_layout
 * @var string		$lang
 * @var array		$layoutAttributes
 */
?>
<?php echo $this->element('head'); ?>
<div id="wrapper">
	<?php echo $this->element('layout/header'); ?>
	<div id="content">
		<?php echo $this->fetch('content'); ?>
		<?php var_dump($this->request) ?>
		<div class="row">
			<div class="g--4">
				<p>Nested Grid 4</p>
				<div class="row">
					<div class="g--6">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium autem corporis enim et facilis, illo itaque libero magnam maxime molestiae omnis qui, quis quo reprehenderit repudiandae, similique sint tempora ullam!</div>
					<div class="g--6">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium amet architecto, cumque delectus dignissimos enim illum, ipsa itaque iure libero molestias neque perspiciatis porro provident quo repellat sed sint ut.</div>
				</div>
			</div>
			<div class="g--8">
				<p>Nested Grid 8</p>
				<div class="row">
					<div class="g--6">
						Sub nested 6
						<div class="row">
							<div class="g--6">col6</div>
							<div class="g--6">col6</div>
						</div>
					</div>
					<div class="g--6">col4</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="g5--1">col1</div>
			<div class="g5--3">col3</div>
			<div class="g5--1">col1</div>
		</div>
	</div>
	<?php echo $this->element('layout/footer'); ?>
</div>
<?php echo $this->element('foot'); ?>