<?php

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "Ошибка: файл autoload.php не найден. Пожалуйста, выполните установку зависимостей.";
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../src/config.php';

use App\Controller\LeadController;

try {
    $leadController = new LeadController($config);
    echo $leadController->handleRequest();
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "Произошла ошибка: " . $e->getMessage();
}
