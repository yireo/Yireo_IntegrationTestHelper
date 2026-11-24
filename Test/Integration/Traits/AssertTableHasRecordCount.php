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
        $select = $connection->select()->from($connection->getTableName($tableName), ['COUNT(*)']);
        $actualCount = (int)$connection->fetchOne($select);
        $this->assertEquals($expectedCount, $actualCount);
    }
}
