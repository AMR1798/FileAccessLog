<?php
namespace OCA\FALog\Db;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use Psr\Log\LoggerInterface;

class LogDAO {

    private $db;
    private LoggerInterface $logger;

    protected ?IQueryBuilder $insertLog = null;

    public function __construct(LoggerInterface $logger) {
        /** @var IDBConnection $connection */
		$this->db = \OC::$server->get(IDBConnection::class);
        $this->logger = $logger;
        
    }

    public function log(array $data): int 
	{
		if ($this->insertLog === null) {
			$this->insertLog = $this->db->getQueryBuilder();
			$this->insertLog->insert('falog')
				->values([
					'ip' => $this->insertLog->createParameter('ip'),
					'timestamp' => $this->insertLog->createParameter('timestamp'),
					'file_id' => $this->insertLog->createParameter('file_id'),
					'user_type' => $this->insertLog->createParameter('user_type'),
					'user_id' => $this->insertLog->createParameter('user_id'),
					'shared_by' => $this->insertLog->createParameter('shared_by'),
					'share_owner' => $this->insertLog->createParameter('share_owner'),
					'headers' => $this->insertLog->createParameter('headers'),
				]);
		}

		// store in DB
		$this->insertLog->setParameters($data);
		$this->insertLog->executeStatement();
        $id = $this->insertLog->getLastInsertId();
        $this->logger->warning("inserted log on id $id", ['data' => $data]);
		return $id;
	}
}

?>