<?php
/**
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the below copyright notice.
 *
 * @copyright     Copyright 2013, Frank Förster (http://frankfoerster.com)
 * @link          http://github.com/frankfoerster/wasabi
 * @package       Wasabi
 * @subpackage    Wasabi.Plugin.Cms.View.Helper
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppHelper', 'View/Helper');
App::uses('Collections', 'Core.Lib');
App::uses('CmsPage', 'Cms.Model');

/**
 * @property CoreHtmlHelper $Html
 * @property CoreView $_View
 */

class CmsPageHelper extends AppHelper {

	/**
	 * Helpers used by this helper.
	 *
	 * @var array
	 */
	public $helpers = array(
		'Html' => array(
			'className' => 'Core.CoreHtml'
		)
	);

	/**
	 * Renders a complete tree of pages without the toplevel ul element.
	 *
	 * @param array $pages
	 * @param array $closedPages
	 * @param integer $langId
	 * @param integer|null $level
	 * @return string
	 */
	public function renderTree($pages, $closedPages, $langId, $level = null) {
		if (empty($pages)) {
			$newPageLink = $this->Html->backendLink(__d('cms', 'Please add your first Page'), '/cms/pages/add', array('title' => __d('cms', 'Add your first Page')), true);
			return '<li class="center">' . __d('cms', 'There are no pages yet. %s.', array($newPageLink)) . '</li>';
		}

		$output = '';

		$depth = ($level !== null) ? $level : 1;
		foreach ($pages as $page) {
			$closed = false;
			$classes =  array('page');
			if (in_array($page['CmsPage']['id'], $closedPages)) {
				$closed = true;
				$classes[] = 'closed';
			}
			$collection = CmsPage::getCollection($page);
			$collectionItem = !$collection ? CmsPage::getCollectionItem($page) : false;

			$pageRow =
				'<div class="row">' .
					'<div class="span7">' .//page
						'<div class="page-info">' .
							'<span class="expander wicon-' . ($closed ? 'expand' : 'collapse') . '"></span>' .
							'<span class="wicon-page"></span>' .
							$this->Html->backendLink($page['CmsPage']['name'], '/cms/pages/edit/' . $page['CmsPage']['id'], array('title' => __d('cms', 'Edit this Page')), true) .
							'<div class="page-meta">' .
								'<small class="layout">' . CmsLayoutManager::getLayout($page['CmsPage']['cms_layout'])->getName() . '</small>' .
								($collection ? ' <small class="collection">' . $collection . '</small>' : '') .
								($collectionItem ? ' <small class="collection-item">' . $collectionItem . '</small>' : '') .
							'</div>' .
						'</div>' .
					'</div>' .
					'<div class="span2 center">' .// live edit
						$this->Html->backendLink('<i class="icon-edit"></i>', '/cms/pages/live_edit/' . $page['CmsPage']['id'], array('title' => __d('cms', 'Edit this Page in Live Mode'), 'escape' => false), true) .
					'</div>' .
					'<div class="span2 center">' .// status
						$this->Html->backendLink($page['CmsPage']['status'], '/cms/pages/toggle/' . $page['CmsPage']['id'], array('class' => $page['CmsPage']['status']), true) .
					'</div>' .
					'<div class="span2 center">' .// preview
						$this->Html->link('<i class="icon-preview"></i>', '/page/' . $page['CmsPage']['id'] . '/' . $langId . '/preview', array('title' => __d('cms', 'Preview this Page'), 'target' => '_blank', 'escape' => false)) .
					'</div>' .
					'<div class="span1 center">' .// sort
						'<a href="javascript:void(0)" class="wicon-move move" title="' . __d('cms', 'Change the position of this Page') . '"><i class="icon-move"></i></a>' .
					'</div>' .
					'<div class="span2 center">' .// actions
						$this->Html->backendLink('', '/cms/pages/add/' . $page['CmsPage']['id'], array('title' => __d('cms', 'Add a new child Page'), 'class' => 'wicon-add')) .
						$this->Html->backendConfirmationLink('', '/cms/pages/delete/' . $page['CmsPage']['id'], array(
							'class' => 'wicon-remove',
							'title' => __d('cms', 'Delete this Page'),
							'confirm-title' => __d('cms', 'Delete Page'),
							'confirm-message' => __d('cms', 'Do you really want to delete Page <strong>%s</strong>?', array($page['CmsPage']['name']))
						)) .
						$this->Html->backendLink('', '/cms/pages/copy/' . $page['CmsPage']['id'], array('title' => __d('cms', 'Create a copy of this Page'), 'class' => 'wicon-copy')) .
					'</div>' .
				'</div>';

			if (!empty($page['children'])) {
				$pageRow .= '<ul' . ($closed ? ' style="display: none;"' : '') . '>' . $this->renderTree($page['children'], $closedPages, $langId, $depth + 1) . '</ul>';
			} else {
				$classes[] = 'no-children';
			}

			$output .= '<li class="' . join(' ', $classes) . '" data-cms-page-id="' . $page['CmsPage']['id'] . '">' . $pageRow . '</li>';
		}

		return $output;
	}

}
