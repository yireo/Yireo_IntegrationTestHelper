<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

use Magento\Framework\App\State;
use Magento\TestFramework\ObjectManager;

trait AssertAreaCodeEquals
{
    use GetObjectManager;

    protected function assertAreaCodeEquals(string $expectedAreaCode)
    {
        $this->assertSame($expectedAreaCode, $this->om()->get(State::class)->getAreaCode());
    }
}
