<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Console\Command;

use Magento\Framework\Exception\FileSystemException;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\ConstantNotFound;
use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\FileNotFound;
use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\InvalidContent;
use Yireo\IntegrationTestHelper\Utilities\IntegrationTesting\PhpUnitFile\Constant;

/**
 * Class ToggleTestsCleanupCommand
 * @package Yireo\IntegrationTestHelper\Console\Command
 */
class ToggleTestsCleanupCommand extends Command
{
    /**
     * @var Constant
     */
    private $phpUnitConstant;

    /**
     * ToggleTestsCleanup constructor.
     * @param Constant $phpUnitConstant
     * @param null $name
     */
    public function __construct(
        Constant $phpUnitConstant,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->phpUnitConstant = $phpUnitConstant;
    }

    /**
     * Configure this Symfony command
     */
    protected function configure()
    {
        $this->setName('integration_tests:toggle_tests_cleanup')
            ->setDescription('Toggle the variable TESTS_CLEANUP in dev/tests/integration/phpunit.xml')
            ->addArgument('state', InputArgument::OPTIONAL, 'Set to either "enabled" or "disabled"');
    }

    /**
     * Execute this Symfony command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws ConstantNotFound
     * @throws FileNotFound
     * @throws InvalidContent
     * @throws FileSystemException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->toggleValue('dev/tests/integration/phpunit.xml', $input, $output);
        $this->toggleValue('dev/tests/quick-integration/phpunit.xml', $input, $output);

        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     * @throws ConstantNotFound
     * @throws FileNotFound
     * @throws FileSystemException
     * @throws InvalidContent
     */
    private function toggleValue(string $fileName, InputInterface $input,  OutputInterface $output): bool
    {
        try {
            $this->phpUnitConstant->setFileName($fileName);
        } catch(FileNotFound $fileNotFound) {
            return false;
        }

        $currentValue = $this->phpUnitConstant->getValue('TESTS_CLEANUP');
        $newValue = $this->determineNewValue($input, $currentValue);

        if ($this->phpUnitConstant->changeValue('TESTS_CLEANUP', $newValue) === true) {
            $msg = sprintf(
                'Constant "%s" has been switched from "%s" to "%s" for file "%s"',
                "TESTS_CLEANUP",
                $currentValue,
                $newValue,
                $fileName
            );

            $output->writeln($msg);
            return true;
        }

        $output->writeln('Constant "TESTS_CLEANUP" has not been changed');
        return true;
    }

    /**
     * @param InputInterface $input
     * @param string $currentValue
     * @return string
     */
    private function determineNewValue(InputInterface $input, string $currentValue): string
    {
        $newState = $input->getArgument('state');
        if (in_array($newState, ['enabled', 'disabled'])) {
            return $newState;
        }

        return ($currentValue == 'enabled') ? 'disabled' : 'enabled';
    }
}
