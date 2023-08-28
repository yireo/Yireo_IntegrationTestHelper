<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Utilities\IntegrationTesting;

use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\File\ReadFactory;
use Magento\Framework\Filesystem\File\WriteFactory;
use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\FileNotFound;
use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\InvalidContent;

class InstallConfig
{
    private $configValues = [];

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var ReadFactory
     */
    private $readFactory;

    /**
     * @var string
     */
    private $fileName = 'dev/tests/integration/etc/install-config-mysql.php';

    /**
     * Constant constructor.
     * @param DirectoryList $directoryList
     * @param ReadFactory $readFactory
     */
    public function __construct(
        DirectoryList $directoryList,
        ReadFactory $readFactory
    ) {
        $this->directoryList = $directoryList;
        $this->readFactory = $readFactory;
    }

    /**
     * @return string[]
     */
    public function getConfigValues(): array
    {
        if (empty($this->configValues)) {
            // phpcs:ignore
            $output = exec('php '.__DIR__.'/InstallConfigExec.php '.$this->directoryList->getRoot());
            $this->configValues = json_decode($output, true);
        }

        return $this->configValues;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getConfigValue(string $name): string
    {
        $configValues = $this->getConfigValues();
        return $configValues[$name] ?? '';
    }

    /**
     * Get the file path of the PhpUnitFile
     *
     * @return string
     * @throws FileNotFound
     */
    private function getFilePath(): string
    {
        $file = $this->directoryList->getRoot() . '/' . $this->fileName;
        if (file_exists($file)) {
            return $file;
        }

        $file = $file . '.dist';
        if (file_exists($file)) {
            return $file;
        }

        throw new FileNotFound(__(sprintf('%s not found', $file)));
    }
}
