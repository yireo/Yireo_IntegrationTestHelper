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

        if (preg_match($this->getRegex($name), $content, $match)) {
            return htmlspecialchars_decode($match[1], ENT_XML1 | ENT_QUOTES);
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

        $newValue = '<const name="' . $this->escape($name) . '" value="' . $this->escape($value) . '"/>';
        $newContent = preg_replace_callback($this->getRegex($name), fn () => $newValue, $content);

        if (!is_string($newContent) || $newContent === $content) {
            return false;
        }

        $this->phpUnitFile->writeContent($newContent);
        return true;
    }

    private function getRegex(string $name): string
    {
        return '/<const\s+name="' . preg_quote($this->escape($name), '/') . '"[^>]*?\svalue="([^"]*)"[^>]*>/';
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES);
    }
}
