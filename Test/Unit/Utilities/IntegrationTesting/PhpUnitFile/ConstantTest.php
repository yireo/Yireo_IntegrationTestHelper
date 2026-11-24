<?php
declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Unit\Utilities\IntegrationTesting\PhpUnitFile;

use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Exception\IntegrationTesting\PhpUnitFile\ConstantNotFound;
use Yireo\IntegrationTestHelper\Utilities\IntegrationTesting\PhpUnitFile;
use Yireo\IntegrationTestHelper\Utilities\IntegrationTesting\PhpUnitFile\Constant;

class ConstantTest extends TestCase
{
    private ?string $writtenContent = null;

    public function testGetValue(): void
    {
        $constant = $this->getConstant($this->getXml('<const name="TESTS_CLEANUP" value="enabled"/>'));
        $this->assertSame('enabled', $constant->getValue('TESTS_CLEANUP'));
    }

    public function testGetValueDecodesXmlEntities(): void
    {
        $constant = $this->getConstant($this->getXml('<const name="FOO" value="a&amp;b&quot;c"/>'));
        $this->assertSame('a&b"c', $constant->getValue('FOO'));
    }

    public function testGetValueThrowsExceptionWhenConstantIsMissing(): void
    {
        $constant = $this->getConstant($this->getXml('<const name="TESTS_CLEANUP" value="enabled"/>'));
        $this->expectException(ConstantNotFound::class);
        $constant->getValue('TESTS_MAGENTO_MODE');
    }

    public function testGetValueTreatsNameAsLiteral(): void
    {
        $constant = $this->getConstant($this->getXml('<const name="TESTS_CLEANUP" value="enabled"/>'));
        $this->expectException(ConstantNotFound::class);
        $constant->getValue('.*');
    }

    public function testChangeValue(): void
    {
        $constant = $this->getConstant($this->getXml('<const name="TESTS_CLEANUP" value="enabled"/>'));
        $this->assertTrue($constant->changeValue('TESTS_CLEANUP', 'disabled'));
        $this->assertStringContainsString('<const name="TESTS_CLEANUP" value="disabled"/>', (string)$this->writtenContent);
    }

    public function testChangeValueOnlyChangesNamedConstantOnSameLine(): void
    {
        $xml = $this->getXml(
            '<const name="TESTS_CLEANUP" value="enabled"/><const name="TESTS_MAGENTO_MODE" value="developer"/>'
        );
        $constant = $this->getConstant($xml);
        $this->assertTrue($constant->changeValue('TESTS_CLEANUP', 'disabled'));
        $this->assertStringContainsString(
            '<const name="TESTS_CLEANUP" value="disabled"/><const name="TESTS_MAGENTO_MODE" value="developer"/>',
            (string)$this->writtenContent
        );
    }

    public function testChangeValueEscapesValue(): void
    {
        $constant = $this->getConstant($this->getXml('<const name="FOO" value="bar"/>'));
        $this->assertTrue($constant->changeValue('FOO', '"/><evil attr="$1\\1'));
        $this->assertStringContainsString(
            '<const name="FOO" value="&quot;/&gt;&lt;evil attr=&quot;$1\\1"/>',
            (string)$this->writtenContent
        );
        $this->assertStringNotContainsString('<evil', (string)$this->writtenContent);
    }

    public function testChangeValueReturnsFalseWhenConstantIsMissing(): void
    {
        $constant = $this->getConstant($this->getXml('<const name="FOO" value="bar"/>'));
        $this->assertFalse($constant->changeValue('TESTS_CLEANUP', 'disabled'));
        $this->assertNull($this->writtenContent);
    }

    public function testChangeValueReturnsFalseWhenValueIsUnchanged(): void
    {
        $constant = $this->getConstant($this->getXml('<const name="TESTS_CLEANUP" value="enabled"/>'));
        $this->assertFalse($constant->changeValue('TESTS_CLEANUP', 'enabled'));
        $this->assertNull($this->writtenContent);
    }

    private function getConstant(string $content): Constant
    {
        $this->writtenContent = null;
        $phpUnitFile = $this->createMock(PhpUnitFile::class);
        $phpUnitFile->method('getContent')->willReturn($content);
        $phpUnitFile->method('writeContent')->willReturnCallback(function (string $newContent): bool {
            $this->writtenContent = $newContent;
            return true;
        });

        return new Constant($phpUnitFile);
    }

    private function getXml(string $constants): string
    {
        return "<phpunit>\n    <php>\n        " . $constants . "\n    </php>\n</phpunit>\n";
    }
}
