<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits\Adminhtml;

use Magento\Framework\Acl\Builder;
use Magento\Framework\App\ObjectManager;

trait AssertAclResourceExists
{
    public function assertAclResourceExists(string $aclResourceId)
    {
        $aclBuilder = ObjectManager::getInstance()->get(Builder::class);
        $aclBuilder->resetRuntimeAcl();
        $acl = $aclBuilder->getAcl();
        $msg = 'No ACL "' . $aclResourceId . '" found. Existing resources: ' . implode(', ', $acl->getResources());
        $this->assertTrue($acl->has($aclResourceId), $msg);
    }
}
