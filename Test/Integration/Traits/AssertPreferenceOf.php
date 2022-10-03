<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\TestFramework\ObjectManager;

trait AssertPreferenceOf
{
    protected function assertPreferenceOf(string $expectedClass, string $injectableClass)
    {
        $objectManager = ObjectManager::getInstance();
        $this->assertInstanceOf($expectedClass, $objectManager->get($injectableClass));
    }
}
