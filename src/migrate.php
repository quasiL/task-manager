<?php

require_once dirname(__DIR__, 1) . '/vendor/autoload.php';
require_once __DIR__ . '/migrations/create_tasks_table.php';

use App\Database\Database;

new Database();

$action = $argv[1] ?? 'up';

match ($action) {
  'up'   => CreateDatabaseSchema::up(),
  'down' => CreateDatabaseSchema::down(),
  default => exit("Invalid command. Use 'up' or 'down'.\n")
};

echo "Migration '$action' completed.\n";
