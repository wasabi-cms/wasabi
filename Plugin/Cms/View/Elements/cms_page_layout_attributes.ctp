<table class="list layout-attributes">
	<thead>
	<tr>
		<th class="t4"><?php echo __d('cms', 'Field') ?></th>
		<th class="t12"><?php echo __d('cms', 'Content') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php if (!empty($this->data['CmsPageLayoutAttribute'])) {
		foreach ($this->data['CmsPageLayoutAttribute'] as $key => $attr) {
			?>
			<tr>
				<td><?php
					echo $this->Form->label('CmsPageLayoutAttribute.' . $key . '.content', $attr['name']);
					if (isset($attr['id'])) {
						echo $this->Form->input('CmsPageLayoutAttribute.' . $key . '.id', array('type' => 'hidden'));
					}
					echo $this->Form->input('CmsPageLayoutAttribute.' . $key . '.cms_layout', array('type' => 'hidden'));
					echo $this->Form->input('CmsPageLayoutAttribute.' . $key . '.cms_layout_attribute', array('type' => 'hidden'));
					?></td>
				<td><?php
					switch ($attr['type']) {
//						case 'page_id':
//							$pages = $OOPage->generatetreelist(null, null, null, '___');
//							echo $this->Form->input('PagesAttribute.'. $i .'.content', array('label' => $a['Attribute']['name'].':', 'type' => 'select', 'options' => $pages, 'value' => $value));
//							break;
//
						case 'select':
							if (isset($attr['options']) && !empty($attr['options'])) {
								$empty = (isset($attr['empty'])) ? $attr['empty'] : false;
								$default = (isset($attr['default']) && $attr['default'] !== '' && !isset($this->request->data['CmsPageLayoutAttribute'][$key]['content'])) ? $attr['default'] : false;
								$options = array(
									'label' => false,
									'type' => 'select',
									'options' => $attr['options'],
									'empty' => $empty
								);
								if ($default !== false) {
									$options['value'] = $default;
								}
								echo $this->Form->input('CmsPageLayoutAttribute.'. $key .'.content', $options);
							}
							break;

						case 'radio':
							$params = explode('|', $attr['params']);
							$options = array();
							foreach ($params as $p) {
								$parts = explode(':', $p);
								$options[$parts[0]] = $parts[1];
							}
							echo $this->Form->input('CmsPageLayoutAttribute.'. $key .'.content', array('legend' => false, 'type' => 'radio', 'options' => $options));
							break;

						case 'date':
							echo $this->Form->input('CmsPageLayoutAttribute.'. $key .'.content', array('type' => 'hidden', 'class' => 'datepicker'));
							break;

						case 'datetime':
							echo $this->Form->input('CmsPageLayoutAttribute.'. $key .'.content', array('type' => 'hidden', 'class' => 'datetimepicker'));
							break;

						case 'image':
							echo $this->Form->input('CmsPageLayoutAttribute.'. $key .'.content', array('type' => 'hidden'));
							break;

						default:
							$options = array(
								'label' => false,
								'type' => $attr['type']
							);
							if ($options['type'] === 'textarea') {
								$options['rows'] = 1;
							}
							echo $this->Form->input('CmsPageLayoutAttribute.'. $key .'.content', $options);
					}
					?></td>
			</tr>
		<?php
		}
	} else {
		echo '<tr><td colspan="2">'.__d('cms', 'This Layout has no attributes.').'</td></tr>';
	}
	?>
	</tbody>
</table>
<small><?php echo __d('cms', 'Layout fields allow you to customize the layout for this page.') ?></small>