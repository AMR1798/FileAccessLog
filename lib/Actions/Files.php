<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2016 Lukas Reschke <lukas@statuscode.ch>
 *
 * @author Joas Schilling <coding@schilljs.com>
 * @author Lukas Reschke <lukas@statuscode.ch>
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\FALog\Actions;


use Psr\Log\LoggerInterface;
use \OCA\AdminAudit\Actions\Action;
use OCA\FALog\CurrentUser;
use OCA\FALog\Db\LogDAO;
// use OCA\Files_Sharing\ISharedMountPoint;
use OCA\Files_Sharing\SharedMount;
use OCP\Share\IManager;
use OCP\Share\IShare;
use OCP\Share\Exceptions\ShareNotFound;

/**
 * Class Files logs the actions to files
 *
 * @package OCA\AdminAudit\Actions
 */
class Files extends Action
{
	private LoggerInterface $logger;
	private LogDAO $db;
	private CurrentUser $user;

	private IManager $shareManager;

	public function __construct(
		LoggerInterface $logger,
	) {
		$this->logger = $logger;
		$this->db = new LogDAO($logger);
		$this->user = new CurrentUser();
		$this->shareManager = \OC::$server->getShareManager();
	}
	/**
	 * Logs file read actions
	 *
	 * @param array $params
	 */
	public function read(array $params): void
	{
		$view = \OC\Files\Filesystem::getView();
		$fileInfo = $view->getFileInfo($params['path']);

		
		$mount = $fileInfo->getMountPoint();
		// log access on shared file only
		if (!($mount instanceof SharedMount)) {
			return;
		}
		$share = $mount->getShare();

		
		$share = $this->getShareById($share->getId());
		$id = $fileInfo->getId();
		$this->logger->warning('File accessed1', ['id' => $id]);
		$data = [
			'ip' => $this->user->getIp(),
			'timestamp' => time(),
			'file_id' => $fileInfo->getId(),
			'user_type' => $this->user->getUserType(),
			'user_id' => $this->user->getUserId(),
			'shared_by' => $share->getSharedBy(),
			'share_owner' => $share->getShareOwner(),
			'headers' => $this->user->getAllheaders()
		];
		$this->db->log($data);

	}

	/**
	 * Since we have multiple providers but the OCS Share API v1 does
	 * not support this we need to check all backends.
	 *
	 * @param string $id
	 * @return \OCP\Share\IShare
	 * @throws ShareNotFound
	 */
	private function getShareById(string $id): IShare
	{
		$share = null;

		// First check if it is an internal share.
		try {
			$share = $this->shareManager->getShareById('ocinternal:' . $id, $this->user->getUserId());
			return $share;
		} catch (ShareNotFound $e) {
			// Do nothing, just try the other share type
		}


		try {
			if ($this->shareManager->shareProviderExists(IShare::TYPE_CIRCLE)) {
				$share = $this->shareManager->getShareById('ocCircleShare:' . $id, $this->user->getUserId());
				return $share;
			}
		} catch (ShareNotFound $e) {
			// Do nothing, just try the other share type
		}

		try {
			if ($this->shareManager->shareProviderExists(IShare::TYPE_EMAIL)) {
				$share = $this->shareManager->getShareById('ocMailShare:' . $id, $this->user->getUserId());
				return $share;
			}
		} catch (ShareNotFound $e) {
			// Do nothing, just try the other share type
		}

		try {
			$share = $this->shareManager->getShareById('ocRoomShare:' . $id, $this->user->getUserId());
			return $share;
		} catch (ShareNotFound $e) {
			// Do nothing, just try the other share type
		}

		try {
			if ($this->shareManager->shareProviderExists(IShare::TYPE_DECK)) {
				$share = $this->shareManager->getShareById('deck:' . $id, $this->user->getUserId());
				return $share;
			}
		} catch (ShareNotFound $e) {
			// Do nothing, just try the other share type
		}

		try {
			if ($this->shareManager->shareProviderExists(IShare::TYPE_SCIENCEMESH)) {
				$share = $this->shareManager->getShareById('sciencemesh:' . $id, $this->user->getUserId());
				return $share;
			}
		} catch (ShareNotFound $e) {
			// Do nothing, just try the other share type
		}

		if (!$this->shareManager->outgoingServer2ServerSharesAllowed()) {
			throw new ShareNotFound();
		}
		$share = $this->shareManager->getShareById('ocFederatedSharing:' . $id, $this->user->getUserId());

		return $share;
	}
}
