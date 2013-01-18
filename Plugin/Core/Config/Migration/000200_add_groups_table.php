<?php
class AddGroupsTable extends CakeMigration {

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
				'groups' => array(
					'id' => array(
						'type' => 'integer',
						'key' => 'primary'
					),
					'name' => array(
						'type' => 'string',
						'null' => false
					),
					'user_count' => array(
						'type' => 'integer',
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
				'groups'
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
		if ($direction === 'up') {
			$group = ClassRegistry::init('Core.Group');
			$group->create(array(
				'name' => 'Administrator'
			));
			$group->save();
		}
		return true;
	}
}
