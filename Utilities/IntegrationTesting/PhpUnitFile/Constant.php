<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Utilities\IntegrationTesting\PhpUnitFile;

use Magento\Framework\Exception\FileSystemException;

use Yireo\IntegrationTestHelper\Utilities\IntegrationTesting\PhpUnitFile;
use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\ConstantNotFound;
use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\FileNotFound;
use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\InvalidContent;

class Constant
{
    /**
     * @var PhpUnitFile
     */
    private $phpUnitFile;

    /**
     * Constant constructor.
     *
     * @param PhpUnitFile $phpUnitFile
     */
    public function __construct(
        PhpUnitFile $phpUnitFile
    ) {
        $this->phpUnitFile = $phpUnitFile;
    }

    /**
     * @param string $fileName
     * @throws FileNotFound
     */
    public function setFileName(string $fileName)
    {
        $this->phpUnitFile->setFileName($fileName);
    }

    /**
     * @param string $name
     * @return string
     * @throws FileNotFound
     * @throws ConstantNotFound
     * @throws InvalidContent
     */
    public function getValue(string $name): string
    {
        $content = $this->phpUnitFile->getContent();

        if (preg_match('/<const name="' . $name . '"(.*)value="([^\"]+)"([^>]+)>/', $content, $match)) {
            return $match[2];
        }

        throw new ConstantNotFound((string)__('Unable to find constant'));
    }

    /**
     * @param string $name
     * @param string $value
     * @return bool
     * @throws FileNotFound
     * @throws FileSystemException
     * @throws InvalidContent
     */
    public function changeValue(string $name, string $value): bool
    {
        $content = $this->phpUnitFile->getContent();

        $newValue = '<const name="' . $name . '" value="' . $value . '"/>';
        $regex = '/<const name="' . $name . '"(.*)value="([^\"]+)"([^>]+)>/';

        if ($newContent = preg_replace($regex, $newValue, $content)) {
            $this->phpUnitFile->writeContent($newContent);
            return true;
        }

        return false;
    }
}
