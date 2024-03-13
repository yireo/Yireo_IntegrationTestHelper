<?php
declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Utilities;

use InvalidArgumentException;

class DisableModules
{
    private string $applicationRoot;
    private array $disableModules = [];
    private array $existingModules = [];

    /**
     * @param string $applicationRoot
     */
    public function __construct(string $applicationRoot)
    {
        $this->setApplicationRoot($applicationRoot);
        $this->existingModules = $this->getModulesFromConfig();
    }

    /**
     * Setup the Magento application root
     *
     * @param string $applicationRoot
     * @return void
     */
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

    /**
     * Disable all available modules (as they are listed in the app/etc/config.php file
     *
     * @return $this
     */
    public function disableAll(): DisableModules
    {
        $this->disableModules = array_keys($this->existingModules);
        return $this;
    }

    /**
     * Enable all Magento core modules
     *
     * @return $this
     */
    public function enableMagento(): DisableModules
    {
        $this->disableModules = array_filter($this->disableModules, fn($module) => !preg_match('/^Magento_/', $module));
        $this->disableByPattern('SampleData');
        $this->disableByPattern('Magento_AdminAnalytics');
        return $this;
    }

    /**
     * Enable a specific module by looking up the environment variable MAGENTO_MODULE
     *
     * @return $this
     */
    public function enableByMagentoModuleEnv(): DisableModules
    {
        if (empty($_SERVER['MAGENTO_MODULE'])) {
            return $this;
        }

        $this->enableByName($_SERVER['MAGENTO_MODULE']);
        return $this;
    }

    /**
     * Enable a specific module by its name (like "Foo_Bar")
     *
     * @return $this
     */
    public function enableByName(string $moduleName): DisableModules
    {
        $moduleNames = explode(',', $moduleName);
        $this->disableModules = array_filter($this->disableModules, fn($module) => !in_array($module, $moduleNames));
        return $this;
    }

    /**
     * Enable a specific modules
     *
     * @return $this
     */
    public function enableByPattern(string $pattern): DisableModules
    {
        $this->disableModules = array_filter($this->disableModules, fn($module) => !stristr($module, $pattern));
        return $this;
    }

    /**
     * Enable a specific modules
     *
     * @return $this
     */
    public function enableByRegexPattern(string $regex): DisableModules
    {
        $this->disableModules = array_filter($this->disableModules, fn($module) => !preg_match($regex, $module));
        return $this;
    }

    /**
     * Include all modules with a certain pattern
     *
     * @return $this
     */
    public function disableByPattern(string $pattern): DisableModules
    {
        foreach ($this->existingModules as $moduleName => $moduleStatus) {
            if (stristr($moduleName, $pattern)) {
                $this->disableModules[] = $moduleName;
            }
        }

        return $this;
    }

    /**
     * Enable the Swissup SearchMysqlLegacy module (to skip ElasticSearch)
     *
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
     *
     * @return $this
     */
    public function disableMagentoInventory(): DisableModules
    {
        return $this->disableByPattern('Magento_Inventory');
    }

    /**
     * Include all modules that are disabled
     *
     * @return $this
     */
    public function disableGraphQl(): DisableModules
    {
        if (!empty($_SERVER['MAGENTO_GRAPHQL']) && (int)$_SERVER['MAGENTO_GRAPHQL'] === 1) {
            return $this;
        }

        return $this->disableByPattern('GraphQl');
    }

    /**
     * Get all modules from the configuration
     *
     * @return array
     */
    public function getModulesFromConfig(): array
    {
        $configFile = $this->applicationRoot . '/app/etc/config.php';
        if (false === file_exists($configFile)) {
            return [];
        }

        // phpcs:ignore
        $config = require($configFile);
        return $config['modules'];
    }


    /**
     * Include all modules that are disabled in the global configuration
     *
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
     * Check if a given module is enabled in this DisableModules configuration
     *
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
     * Get all modules to disable
     *
     * @return array
     */
    public function get(): array
    {
        $this->disableDisabledAnyway();
        sort($this->disableModules);
        return array_unique($this->disableModules);
    }

    /**
     * Return all modules as a CSV string
     *
     * @return string
     */
    public function toString(): string
    {
        return implode(',', $this->get());
    }
}
