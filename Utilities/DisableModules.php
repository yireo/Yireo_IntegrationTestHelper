<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Utilities;

use InvalidArgumentException;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Component\ComponentRegistrar;

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

        if ((int)$this->getVariable('DISABLE_INVENTORY') === 1) {
            $this->disableMagentoInventory();
        }

        return $this;
    }

    /**
     * Enable a specific module by looking up the environment variable MAGENTO_MODULE
     *
     * @return $this
     */
    public function enableByMagentoModuleEnv(): DisableModules
    {
        $moduleName = $this->getVariable('MAGENTO_MODULE');
        if (empty($moduleName)) {
            return $this;
        }

        $this->enableByName($moduleName);
        return $this;
    }

    /**
     * Enable a specific module by looking up the environment variable MAGENTO_MODULE
     *
     * @return $this
     */
    public function enableByMagentoModuleFolderEnv(): DisableModules
    {
        $moduleFolder = $this->getVariable('MAGENTO_MODULE_FOLDER');
        if (empty($moduleFolder)) {
            return $this;
        }

        if (false === is_dir($moduleFolder)) {
            $moduleFolder = $this->applicationRoot.'/'.$moduleFolder;
        }

        if (false === is_dir($moduleFolder)) {
            return $this;
        }

        if (false === is_file($moduleFolder.'/etc/module.xml')) {
            return $this;
        }

        $moduleXmlFile = $moduleFolder.'/etc/module.xml';
        foreach ($this->getModuleNamesFromModuleXml($moduleXmlFile) as $moduleName) {
            $this->enableByName($moduleName);
        }

        if (is_file($moduleFolder.'/MODULE.json')) {
            $data = json_decode(file_get_contents($moduleFolder.'/MODULE.json'), true);
            if (isset($data['modules']) && is_array($data['modules'])) {
                foreach ($data['modules'] as $module) {
                    $this->enableByName($module);
                }
            }
        }

        return $this;
    }

    private function getModuleNamesFromModuleXml(string $moduleXmlFile): array
    {
        $moduleNames = [];
        $moduleXml = file_get_contents($moduleXmlFile);
        $xml = simplexml_load_string($moduleXml, "SimpleXMLElement");
        $moduleName = (string)$xml->module['name'];
        $moduleNames[] = $moduleName;

        if ($xml->module->sequence) {
            foreach ($xml->module->sequence->module as $sequence) {
                $moduleNames[] = (string)$sequence['name'];
            }
        }

        return $moduleNames;
    }

    /**
     * Enable a specific module by its name (like "Foo_Bar")
     *
     * @return $this
     */
    public function enableByName(string $moduleName): DisableModules
    {
        $moduleNames = explode(',', $moduleName);
        $moduleMap = $this->getModuleMap();
        foreach ($moduleNames as $moduleName) {
            if (!isset($moduleMap[$moduleName])) {
                continue;
            }

            $modulePath = $moduleMap[$moduleName];
            $moduleXmlFile = $modulePath.'/etc/module.xml';
            $moduleNames = array_merge($moduleNames, $this->getModuleNamesFromModuleXml($moduleXmlFile));
        }

        $this->disableModules = array_filter($this->disableModules, fn($module) => !in_array($module, $moduleNames));

        return $this;
    }

    private function getModuleMap(): array
    {
        $moduleMapFile = $this->applicationRoot.'/dev/tests/integration/module-map.json';
        if (!file_exists($moduleMapFile)) {
            return [];
        }

        return json_decode(file_get_contents($moduleMapFile), true);
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
        if ((int)$this->getVariable('MAGENTO_GRAPHQL') === 1) {
            return $this;
        }

        return $this->disableByPattern('GraphQl');
    }

    /**
     * Include all modules that are disabled in the global configuration
     *
     * @return $this
     */
    public function disableDisabledAnyway(): DisableModules
    {
        foreach ($this->existingModules as $moduleName => $moduleStatus) {
            if ($moduleStatus === 0) {
                $this->disableModules[] = $moduleName;
            }
        }

        return $this;
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
        $this->enableByMagentoModuleEnv();
        $this->enableByMagentoModuleFolderEnv();
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

        if (!is_file($applicationRoot . '/app/etc/di.xml')) {
            $msg = 'Application root "' . $applicationRoot . '" does not contain a Magento installation';
            throw new InvalidArgumentException($msg);
        }

        $this->applicationRoot = $applicationRoot;
    }


    /**
     * @param string $variableName
     * @return mixed
     */
    private function getVariable(string $variableName): mixed
    {
        if (isset($_ENV[$variableName]) && !empty($_ENV[$variableName])) {
            return $_ENV[$variableName];
        }

        if (isset($_SERVER[$variableName]) && !empty($_SERVER[$variableName])) {
            return $_SERVER[$variableName];
        }

        return null;
    }
}
