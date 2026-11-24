<?php declare(strict_types=1);
// phpcs:ignoreFile

if (PHP_SAPI !== 'cli') {
    exit(1);
}

$magentoRoot = realpath((string)($_SERVER['argv'][1] ?? ''));
if ($magentoRoot === false || !is_dir($magentoRoot)) {
    fwrite(STDERR, 'Invalid Magento root' . PHP_EOL);
    exit(1);
}

$autoloadFile = $magentoRoot . '/dev/tests/integration/framework/autoload.php';
if (!is_file($autoloadFile)) {
    fwrite(STDERR, 'Integration test framework not found' . PHP_EOL);
    exit(1);
}

require_once $autoloadFile;

$file = $magentoRoot . '/dev/tests/integration/etc/install-config-mysql.php';
if (!is_file($file)) {
    $file = $magentoRoot . '/dev/tests/integration/etc/install-config-mysql.php.dist';
}

if (!is_file($file)) {
    fwrite(STDERR, 'Install configuration not found' . PHP_EOL);
    exit(1);
}

$values = require $file;
if (!is_array($values)) {
    fwrite(STDERR, 'Install configuration is not an array' . PHP_EOL);
    exit(1);
}

echo json_encode($values);
