<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResourceConnection\ConnectionFactory;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Finder\Finder;

trait AssertDatabaseQueryCount
{
    public function getDatabaseQueryCount()
    {
        $om = ObjectManager::getInstance();
        $resourceConnection = $om->get(ResourceConnection::class);
        $connection = $resourceConnection->getConnection();
        $row = $connection->fetchRow('SHOW GLOBAL STATUS LIKE "Queries"');
        return (int)$row['Value'];
    }
    
    public function assertDatabaseQueryCount(int $queryCount)
    {
        $this->assertSame($queryCount + 1, $this->getDatabaseQueryCount());
    }
}
