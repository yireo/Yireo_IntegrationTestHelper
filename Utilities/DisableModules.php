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
        $this->enableByName(implode(',', $this->getModuleNamesFromModuleXml($moduleXmlFile)));

        if (is_file($moduleFolder.'/MODULE.json')) {
            $data = json_decode(file_get_contents($moduleFolder.'/MODULE.json'), true);
            if (isset($data['modules']) && is_array($data['modules'])) {
                $this->enableByName(implode(',', $data['modules']));
            }
        }

        return $this;
    }

    private function getModuleNamesFromModuleXml(string $moduleXmlFile): array
    {
        $moduleNames = [];
        if (false === is_file($moduleXmlFile)) {
            return $moduleNames;
        }

        $moduleXml = file_get_contents($moduleXmlFile);
        $xml = simplexml_load_string($moduleXml, "SimpleXMLElement");
        if (false === $xml || !isset($xml->module)) {
            return $moduleNames;
        }

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
        $moduleNames = $this->resolveDependencies(array_filter(array_map('trim', explode(',', $moduleName))));
        $this->disableModules = array_filter($this->disableModules, fn($module) => !in_array($module, $moduleNames));

        return $this;
    }

    private function resolveDependencies(array $moduleNames): array
    {
        $moduleMap = $this->getModuleMap();
        $resolved = [];
        $queue = array_values($moduleNames);
        while (!empty($queue)) {
            $moduleName = array_shift($queue);
            if (isset($resolved[$moduleName])) {
                continue;
            }

            $resolved[$moduleName] = true;
            if (!isset($moduleMap[$moduleName])) {
                continue;
            }

            $moduleXmlFile = $moduleMap[$moduleName].'/etc/module.xml';
            foreach ($this->getModuleNamesFromModuleXml($moduleXmlFile) as $dependency) {
                if (!isset($resolved[$dependency])) {
                    $queue[] = $dependency;
                }
            }
        }

        return array_keys($resolved);
    }

    private function getModuleMap(): array
    {
        $moduleMap = [];
        if (class_exists(ComponentRegistrar::class)) {
            $moduleMap = (new ComponentRegistrar())->getPaths(ComponentRegistrar::MODULE);
        }

        $moduleMapFile = $this->applicationRoot.'/dev/tests/integration/module-map.json';
        if (file_exists($moduleMapFile)) {
            $moduleMap = array_merge((array)json_decode(file_get_contents($moduleMapFile), true), $moduleMap);
        }

        return $moduleMap;
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
        if (!is_array($config) || !isset($config['modules']) || !is_array($config['modules'])) {
            return [];
        }

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
