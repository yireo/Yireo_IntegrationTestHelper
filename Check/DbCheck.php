<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Check;

use PDO;
use PDOException;
use Yireo\IntegrationTestHelper\Utilities\CurrentInstallConfig;

class DbCheck
{
    private CurrentInstallConfig $currentInstallConfig;

    public function __construct(
        CurrentInstallConfig $currentInstallConfig
    ) {
        $this->currentInstallConfig = $currentInstallConfig;
    }

    public function checkDbConnection(): bool
    {
        $config = $this->currentInstallConfig->getValues();
        $host = (string)($config['db-host'] ?? '');
        $port = (string)($config['db-port'] ?? '');
        $user = (string)($config['db-user'] ?? '');
        $pass = (string)($config['db-password'] ?? '');
        $dbName = (string)($config['db-name'] ?? '');

        foreach ([$host, $port, $dbName] as $dsnPart) {
            if (str_contains($dsnPart, ';')) {
                return false;
            }
        }

        $dsn = 'mysql:host=' . $host . ';dbname=' . $dbName;
        if ($port !== '') {
            $dsn .= ';port=' . $port;
        }

        try {
            new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 3]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
