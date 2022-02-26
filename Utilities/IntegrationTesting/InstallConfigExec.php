<?php declare(strict_types=1);

$magentoRoot = $argv[1];
require_once $magentoRoot . '/dev/tests/integration/framework/autoload.php';

$file = $magentoRoot . '/dev/tests/integration/etc/install-config-mysql.php';
if (!file_exists($file)) {
    $file = $magentoRoot . '/dev/tests/integration/etc/install-config-mysql.php.dist';
}

echo json_encode(require_once($file));
