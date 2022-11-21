<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Utilities;

use InvalidArgumentException;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\DirectoryList;

class DisableModules
{
    private string $applicationRoot;
    private array $disableModules = [];
    private array $existingModules = [];

    public function __construct(string $applicationRoot)
    {
        $this->setApplicationRoot($applicationRoot);
        $this->existingModules = $this->getModulesFromConfig();
    }

    private function setApplicationRoot(string $applicationRoot)
    {
        if (!is_dir($applicationRoot)) {
            $msg = 'Application root "' . $applicationRoot . '" is not a directory';
            throw new InvalidArgumentException($msg);
        }

        if (!is_file($applicationRoot . '/app/etc/config.php')) {
            $msg = 'Application root "' . $applicationRoot . '" does not contain a Magento installation';
            throw new InvalidArgumentException($msg);
        }

        $this->applicationRoot = $applicationRoot;
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

    public function enableByMagentoModuleEnv(): DisableModules
    {
        if (empty($_SERVER['MAGENTO_MODULE'])) {
            return $this;
        }

        $this->enableByName($_SERVER['MAGENTO_MODULE']);
        return $this;
    }

    /**
     * Enable a specific modules
     * @return $this
     */
    public function enableByName(string $moduleName): DisableModules
    {
        $this->disableModules = array_filter($this->disableModules, fn($module) => $module !== $moduleName);
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
    public function disableByPattern(string $pattern): DisableModules
    {
        foreach ($this->existingModules as $moduleName => $moduleStatus) {
            if (strstr($moduleName, $pattern)) {
                $this->disableModules[] = $moduleName;
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function enableSwissupSearchMysqlLegacy(): DisableModules
    {
        if (!$this->isModuleEnabled('Swissup_SearchMysqlLegacy')) {
            return $this;
        }

        $this->disableByPattern('Magento_InventoryElasticsearch');
        $this->disableByPattern('Magento_Elasticsearch7');
        $this->disableByPattern('Magento_Elasticsearch6');
        $this->disableByPattern('Magento_Elasticsearch');
        return $this;
    }

    /**
     * Include all modules that are disabled
     * @return $this
     */
    public function disableMagentoInventory(): DisableModules
    {
        return $this->disableByPattern('Magento_Inventory');
    }

    /**
     * Include all modules that are disabled
     * @return $this
     */
    public function disableGraphQl(): DisableModules
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
    private function disableDisabledAnyway(): DisableModules
    {
        foreach ($this->existingModules as $moduleName => $moduleStatus) {
            if ($moduleStatus === 0) {
                $this->disableModules[] = $moduleName;
            }
        }

        return $this;
    }

    /**
     * @param string $moduleName
     * @return bool
     */
    public function isModuleEnabled(string $moduleName): bool
    {
        if (!array_key_exists($moduleName, $this->existingModules)) {
            return false;
        }

        return (bool)$this->existingModules[$moduleName];
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $this->disableDisabledAnyway();
        sort($this->disableModules);
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
