<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Utilities;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\DirectoryList;

class DisableModules
{
    private string $applicationRoot;
    private array $disableModules = [];
    private array $existingModules = [];

    public function __construct(string $applicationRoot)
    {
        $this->applicationRoot = $applicationRoot;
        $this->existingModules = $this->getModulesFromConfig();
    }

    public function disableAll(): DisableModules
    {
        $this->disableModules = array_keys($this->existingModules);
        return $this;
    }

    /**
     * Enable all Magento core modules
     * @return $this
     */
    public function enableMagento(): DisableModules
    {
        $this->disableModules = array_filter($this->disableModules, fn($module) => !preg_match('/^Magento_/', $module));
        return $this;
    }

    /**
     * Enable a specific modules
     * @return $this
     */
    public function enableByName(string $moduleName): DisableModules
    {
        $this->disableModules = array_filter($this->disableModules, fn($module) => !$module === $moduleName);
        return $this;
    }

    /**
     * Enable a specific modules
     * @return $this
     */
    public function enableByPattern(string $pattern): DisableModules
    {
        $this->disableModules = array_filter($this->disableModules, fn($module) => !strstr($module, $pattern));
        return $this;
    }

    /**
     * Include all modules with a certain pattern
     * @return $this
     */
    public function disableByPattern(string $pattern)
    {
        foreach ($this->existingModules as $moduleName => $moduleStatus) {
            if (strstr($moduleName, $pattern)) {
                $this->disableModules[] = $moduleName;
            }
        }

        return $this;
    }

    /**
     * Include all modules that are disabled
     * @return $this
     */
    public function disableMagentoInventory()
    {
        return $this->disableByPattern('Magento_Inventory');
    }

    /**
     * Include all modules that are disabled
     * @return $this
     */
    public function disableGraphQl()
    {
        return $this->disableByPattern('GraphQl');
    }

    /**
     * @return array
     */
    public function getModulesFromConfig(): array
    {
        $config = require($this->applicationRoot . '/app/etc/config.php');
        return $config['modules'];
    }


    /**
     * Include all modules that are disabled in the global configuration
     * @return $this
     */
    private function disableDisabledAnyway()
    {
        foreach ($this->existingModules as $moduleName => $moduleStatus) {
            if ($moduleStatus === 0) {
                $this->disableModules[] = $moduleName;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $this->disableDisabledAnyway();
        return array_unique($this->disableModules);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return implode(',', $this->get());
    }
}
