<?php

declare(strict_types=1);

namespace OCA\FALog\Listener;

use OCA\Files_Sharing\Event\ShareLinkAccessedEvent;
use OCA\FAlog\AppInfo\Application;
use OCA\FALog\CurrentUser;
use OCP\AppFramework\Services\IInitialState;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Util;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use Psr\Log\LoggerInterface;
use OCA\FALog\Db\LogDAO;

class ShareLinkAccessedListener implements IEventListener
{
	private LogDAO $db;
	private LoggerInterface $logger;

	public function __construct(
		LoggerInterface $logger
	) {
		$this->db = new LogDAO($logger);
		$this->logger = $logger;
	}
	public function handle(Event $event): void
	{
		if (!($event instanceof ShareLinkAccessedEvent)) {
			return;
		}
		$user = new CurrentUser();

		$data = [
			'ip' => $user->getIp(),
			'timestamp' => time(),
			'file_id' => $event->getShare()->getNodeId(),
			'user_type' => $user->getUserType(),
			'user_id' => $user->getUserId(),
			'shared_by' => $event->getShare()->getSharedBy(),
			'share_owner' => $event->getShare()->getShareOwner(),
			'headers' => $user->getAllheaders()
		];
		$this->db->log($data);
	}

}
