<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits\Adminhtml;

use Magento\Framework\Acl\Builder;
use Magento\Framework\App\ObjectManager;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\GetObjectManager;

trait AssertAclResourceExists
{
    use GetObjectManager;

    public function assertAclResourceExists(string $aclResourceId)
    {
        $aclBuilder = $this->om()->get(Builder::class);
        $aclBuilder->resetRuntimeAcl();
        $acl = $aclBuilder->getAcl();
        $msg = 'No ACL "' . $aclResourceId . '" found. Existing resources: ' . implode(', ', $acl->getResources());
        $this->assertTrue($acl->has($aclResourceId), $msg);
    }
}
