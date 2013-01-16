<?php
class AddGroupPermissionsTable extends CakeMigration {

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
				'group_permissions' => array(
					'id' => array(
						'type' => 'integer',
						'key' => 'primary'
					),
					'group_id' => array(
						'type' => 'integer',
						'null' => false
					),
					'path' => array(
						'type' => 'string',
						'null' => false
					),
					'allowed' => array(
						'type' => 'boolean',
						'null' => false,
						'default' => 0
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
				'group_permissions'
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
