<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Check;

use PDO;
use PDOException;
use Laminas\Db\Adapter\Driver\Pdo\ConnectionFactory;
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
        $host = $config['db-host'];
        $user = $config['db-user'];
        $pass = $config['db-password'];
        $dbName = $config['db-name'];

        try {
            $pdo = new PDO('mysql:host=' . $host . ';dbname=' . $dbName, $user, $pass);
            return true;
        } catch (PDOException $e) {
        }

        return false;
    }
}
