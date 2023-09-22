<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\TestFramework\ObjectManager;

trait AssertPreferenceOf
{
    use GetObjectManager;

    protected function assertPreferenceOf(string $expectedClass, string $injectableClass)
    {
        $this->assertInstanceOf($expectedClass, $this->om()->get($injectableClass));
    }
}
