<?php declare(strict_types=1);

namespace Yireo\IntegrationTestHelper\Utilities;

use Laminas\Db\Adapter\Driver\Pdo\ConnectionFactory;
use PDO;

class DbCheck
{
    public function checkDbConnection(array $config = []): bool
    {
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