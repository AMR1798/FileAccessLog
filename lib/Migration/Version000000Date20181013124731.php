<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: AMRMZKR <ammar.muzakkir@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\FALog\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;
use Doctrine\DBAL\Types\JsonType;

class Version000000Date20181013124731 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('falog')) {
			$table = $schema->createTable('falog');
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				
			]);
			$table->addColumn('ip', 'string', [
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('timestamp', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('file_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('user_type', 'string', [
				'notnull' => true,
				'length' => 20
			]);

			$table->addColumn('user_id', 'string', [
				'length' => 200,
				'notnull' => false,
			]);

			$table->addColumn('shared_by', 'string', [
				'length' => 200,
				'notnull' => false,
			]);

			$table->addColumn('share_owner', 'string', [
				'length' => 200,
				'notnull' => false,
			]);

			$table->addColumn('headers', 'json');

			$table->setPrimaryKey(['id']);
			$table->addIndex(['user_id'], 'falog_user_id_index');
			$table->addIndex(['timestamp'], 'falog_timestamp_index');
			$table->addIndex(['user_type'], 'falog_user_type_index');
			$table->addIndex(['file_id'], 'falog_file_id_index');
			$table->addIndex(['ip'], 'falog_ip_index');
			$table->addIndex(['shared_by'], 'falog_shared_by_index');
			$table->addIndex(['share_owner'], 'falog_share_owner_index');
		}
		return $schema;
	}
}
