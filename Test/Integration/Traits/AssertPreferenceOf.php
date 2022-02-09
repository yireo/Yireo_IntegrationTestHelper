<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

trait AssertPreferenceOf
{
    protected function assertPreferenceOf(string $expectedClass, string $injectableClass)
    {
        $this->assertInstanceOf($expectedClass, $this->objectManager->get($injectableClass));
    }
}
