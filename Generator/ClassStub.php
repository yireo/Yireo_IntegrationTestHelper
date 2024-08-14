<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Generator;

class ClassStub
{
    public function __construct(
        private string $moduleName,
        private string $fullQualifiedClassName
    ) {
    }

    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    public function getFullQualifiedClassName(): string
    {
        return trim($this->fullQualifiedClassName, '\\');
    }

    public function getModuleClassPrefix(): string
    {
        return str_replace('_', '\\', $this->moduleName);
    }

    public function getRelativeNamespace(): string
    {
        return str_replace($this->getModuleClassPrefix().'\\', '', $this->getNamespace());
    }

    public function getClassName(): string
    {
        return substr(strrchr($this->getFullQualifiedClassName(), '\\'), 1);
    }

    public function getNamespace(): string
    {
        $length = strlen($this->getFullQualifiedClassName()) - (strlen($this->getClassName()) + 1);

        return substr($this->getFullQualifiedClassName(), 0, $length);
    }
}
