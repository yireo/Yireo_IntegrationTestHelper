<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Utilities\IntegrationTesting;

use Magento\Framework\Filesystem\DirectoryList;

class InstallConfig
{
    private array $configValues = [];

    public function __construct(
        private readonly DirectoryList $directoryList
    ) {
    }

    /**
     * @return string[]
     */
    public function getConfigValues(): array
    {
        if (!empty($this->configValues)) {
            return $this->configValues;
        }

        $command = escapeshellarg(PHP_BINARY)
            . ' ' . escapeshellarg(__DIR__ . '/InstallConfigExec.php')
            . ' ' . escapeshellarg($this->directoryList->getRoot());

        $output = [];
        $resultCode = 1;
        // phpcs:ignore
        exec($command, $output, $resultCode);
        if ($resultCode !== 0) {
            return [];
        }

        $configValues = json_decode(implode("\n", $output), true);
        if (!is_array($configValues)) {
            return [];
        }

        $this->configValues = $configValues;
        return $this->configValues;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getConfigValue(string $name): string
    {
        $configValues = $this->getConfigValues();
        return (string)($configValues[$name] ?? '');
    }
}
