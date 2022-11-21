<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Test\Integration\Traits;

trait AssertNonEmptyValueInArray
{
    protected function assertNonEmptyValueInArray(string $value, array $data)
    {
        $this->assertArrayHasKey($value, $data, var_export($data, true));
        $this->assertNotEmpty($data[$value]);
    }
}
