<?php
declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Unit\Utilities;

use Magento\Framework\Component\ComponentRegistrar;
use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Utilities\DisableModules;

class DisableModulesTest extends TestCase
{
    private static string $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = sys_get_temp_dir().'/disable-modules-test-'.uniqid();
        mkdir(self::$root.'/app/etc', 0777, true);
        touch(self::$root.'/app/etc/di.xml');

        $modules = [
            'Test_DisableModulesA' => ['Test_DisableModulesB'],
            'Test_DisableModulesB' => ['Test_DisableModulesC', 'Test_DisableModulesA'],
            'Test_DisableModulesC' => [],
            'Test_DisableModulesD' => [],
        ];

        $config = [];
        foreach ($modules as $moduleName => $sequence) {
            $config[$moduleName] = 1;
            $modulePath = self::$root.'/modules/'.$moduleName;
            mkdir($modulePath.'/etc', 0777, true);
            file_put_contents($modulePath.'/etc/module.xml', self::getModuleXml($moduleName, $sequence));
            ComponentRegistrar::register(ComponentRegistrar::MODULE, $moduleName, $modulePath);
        }

        file_put_contents(self::$root.'/app/etc/config.php', '<?php return '.var_export(['modules' => $config], true).';');
    }

    public static function tearDownAfterClass(): void
    {
        exec('rm -rf '.escapeshellarg(self::$root));
    }

    public function testEnableByNameEnablesDependenciesTransitively(): void
    {
        $disableModules = (new DisableModules(self::$root))
            ->disableAll()
            ->enableByName('Test_DisableModulesA');

        $this->assertSame(['Test_DisableModulesD'], $this->getDisabledTestModules($disableModules));
    }

    public function testEnableByMagentoModuleFolderEnvEnablesDependenciesTransitively(): void
    {
        $_ENV['MAGENTO_MODULE_FOLDER'] = self::$root.'/modules/Test_DisableModulesA';

        try {
            $disableModules = (new DisableModules(self::$root))
                ->disableAll()
                ->enableByMagentoModuleFolderEnv();
        } finally {
            unset($_ENV['MAGENTO_MODULE_FOLDER']);
        }

        $this->assertSame(['Test_DisableModulesD'], $this->getDisabledTestModules($disableModules));
    }

    private function getDisabledTestModules(DisableModules $disableModules): array
    {
        return array_values(array_filter(
            $disableModules->get(),
            fn($module) => str_starts_with($module, 'Test_DisableModules')
        ));
    }

    private static function getModuleXml(string $moduleName, array $sequence): string
    {
        $sequenceXml = '';
        foreach ($sequence as $dependency) {
            $sequenceXml .= '<module name="'.$dependency.'"/>';
        }

        return '<?xml version="1.0"?><config><module name="'.$moduleName.'"><sequence>'
            .$sequenceXml.'</sequence></module></config>';
    }
}
