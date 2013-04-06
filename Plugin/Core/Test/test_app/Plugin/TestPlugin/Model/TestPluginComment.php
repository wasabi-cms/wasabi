<?php

class TestPluginComment extends Model {

	public $useDbConfig = 'test';

	public $actsAs = array(
		'Core.Relatable'
	);

	public $belongsTo = array(
		'TestPluginPost' => array(
			'className' => 'TestPlugin.TestPluginPost'
		)
	);

	public $recursive = -1;
}
