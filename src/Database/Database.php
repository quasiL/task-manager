<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

class Database
{
  private static $pdo;

  public function __construct()
  {
    $dbFile = dirname(__DIR__, 1) . '/storage/database.sqlite';
    $storageDir = dirname($dbFile);

    if (!is_dir($storageDir)) {
      mkdir($storageDir, 0755, true);
    }

    if (!file_exists($dbFile)) {
      touch($dbFile);
    }

    if (!self::$pdo) {
      self::$pdo = new PDO("sqlite:$dbFile");
      self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
  }

  /**
   * Check if a given table exists in the database.
   *
   * @param string $tableName 
   * @return bool True if the table exists, false otherwise
   */
  public static function tableExists(string $tableName): bool
  {
    $pdo = self::getConnection();
    $stmt = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name = :table");
    $stmt->execute([':table' => $tableName]);

    return $stmt->fetchColumn() !== false;
  }

  /**
   * Get a PDO connection to the database. If the connection doesn't exist, it will be created.
   *
   * @return PDO
   */
  public static function getConnection(): PDO
  {
    if (!self::$pdo) {
      new self();
    }
    return self::$pdo;
  }
}
