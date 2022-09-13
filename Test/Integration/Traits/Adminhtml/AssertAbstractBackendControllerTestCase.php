<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits\Adminhtml;

use Magento\TestFramework\TestCase\AbstractBackendController;

trait AssertAbstractBackendControllerTestCase
{
    private function assertAbstractBackendControllerTestCase()
    {
        $this->assertInstanceOf(AbstractBackendController::class, $this);
    }
}
