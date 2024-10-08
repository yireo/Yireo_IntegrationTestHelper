<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Utilities;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Autoload\AutoloaderRegistry;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Setup\Console\Command\InstallCommand;
use Magento\Setup\Model\ConfigModel;
use Magento\Setup\Model\SearchConfigOptionsList;

class CurrentInstallConfig
{
    private DirectoryList $directoryList;
    private DefaultInstallConfig $defaultInstallConfig;
    private DriverInterface $fileDriver;

    public function __construct(
        DirectoryList $directoryList,
        DefaultInstallConfig $defaultInstallConfig,
        Filesystem $filesystem
    ) {
        $this->directoryList = $directoryList;
        $this->defaultInstallConfig = $defaultInstallConfig;
        $this->fileDriver = $filesystem->getDirectoryWrite(DirectoryList::PUB)->getDriver();
    }

    /**
     * @return string[]
     */
    public function getValues(): array
    {
        $integrationTestsDir = $this->directoryList->getRoot() . '/dev/tests/integration/';

        $testsBaseDir = $integrationTestsDir;
        $autoloadWrapper = AutoloaderRegistry::getAutoloader();

        $autoloadWrapper->addPsr4('Magento\\TestFramework\\', "{$testsBaseDir}/framework/Magento/TestFramework/");
        $autoloadWrapper->addPsr4('Magento\\', "{$testsBaseDir}/testsuite/Magento/");

        $installConfig = $integrationTestsDir . '/etc/install-config-mysql.php';
        if (!$this->fileDriver->isExists($installConfig)) {
            $installConfig = $installConfig . '.dist';
        }

        // phpcs:ignore
        $installConfigValues = require($installConfig);
        $values = $this->defaultInstallConfig->getValues();

        return array_merge($values, $installConfigValues);
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function getValue(string $key)
    {
        $values = $this->getValues();
        if (isset($values[$key])) {
            return $values[$key];
        }

        return null;
    }
}
