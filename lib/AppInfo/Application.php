<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: AMRMZKR <ammar.muzakkir@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\FALog\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'falog';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}
}
