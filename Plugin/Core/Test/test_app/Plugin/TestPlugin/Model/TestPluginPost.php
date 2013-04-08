<?php
/**
 * @property TestPluginAuthor $TestPluginAuthor
 */
class TestPluginPost extends Model {

	public $useDbConfig = 'test';

	public $actsAs = array(
		'Core.Relatable',
		'Core.Translatable' => array(
			'fields' => array(
				'title'
			)
		)
	);

	public $belongsTo = array(
		'TestPluginAuthor' => array(
			'className' => 'TestPlugin.TestPluginAuthor'
		),
		'TestPluginCategory' => array(
			'className' => 'TestPlugin.TestPluginCategory'
		)
	);

	public $hasMany = array(
		'TestPluginComment' => array(
			'className' => 'TestPlugin.TestPluginComment'
		)
	);

	public $recursive = -1;
}
