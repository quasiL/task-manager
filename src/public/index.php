<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\Controllers\TaskController;

$taskController = new TaskController();
$taskController->handleRequest();