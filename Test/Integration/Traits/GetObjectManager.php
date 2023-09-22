<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager as TestObjectManager;
use Magento\Framework\App\ObjectManager as RealObjectManager;

trait GetObjectManager
{
    protected function getObjectManager(): ObjectManagerInterface
    {
        if (isset($this->objectManager) && $this->objectManager instanceof ObjectManagerInterface) {
            return $this->objectManager;
        }

        if (class_exists(Bootstrap::class)) {
            return Bootstrap::getObjectManager();
        }

        if (class_exists(TestObjectManager::class)) {
            return TestObjectManager::getInstance();
        }

        return RealObjectManager::getInstance();
    }

    protected function om(): ObjectManagerInterface
    {
        return $this->getObjectManager();
    }
}
