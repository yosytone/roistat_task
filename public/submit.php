<?php

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../src/config.php';

use App\Controller\LeadController;

$leadController = new LeadController($config);
echo $leadController->handleRequest();

?>