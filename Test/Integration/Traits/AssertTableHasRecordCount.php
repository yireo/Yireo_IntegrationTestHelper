<?php
declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;

trait AssertTableHasRecordCount
{
    public function assertTableHasRecordCount(int $expectedCount, string $tableName)
    {
        $resourceConnection = ObjectManager::getInstance()->get(ResourceConnection::class);
        $connection = $resourceConnection->getConnection();
        $query = 'SELECT COUNT(*) FROM ' . $connection->getTableName($tableName);
        $actualCount = (int)$connection->fetchOne($query);
        $this->assertEquals($expectedCount, $actualCount);
    }
}