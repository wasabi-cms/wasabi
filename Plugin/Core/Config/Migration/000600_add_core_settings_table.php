<?php
class AddCoreSettingsTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'core_settings' => array(
					'id' => array(
						'type' => 'integer',
						'key' => 'primary'
					),
					'application_name' => array(
						'type' => 'string',
						'length' => 255,
						'null' => false
					),
					'enable_caching' => array(
						'type' => 'boolean',
						'null' => false,
						'default' => 0
					),
					'cache_time' => array(
						'type' => 'string',
						'length' => 255,
						'null' => false
					),
					'created' => array(
						'type' => 'datetime',
						'null' => false
					),
					'modified' => array(
						'type' => 'datetime',
						'null' => false
					),
					'indexes' => array(
						'PRIMARY' => array(
							'column' => 'id',
							'unique' => 1
						)
					)
				)
			)
		),
		'down' => array(
			'drop_table' => array(
				'core_settings'
			)
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function after($direction) {
		return true;
	}
}
