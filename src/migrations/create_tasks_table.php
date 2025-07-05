<?php

declare(strict_types=1);

use App\Database\Database;

class CreateDatabaseSchema
{
  public static function up()
  {
    $pdo = Database::getConnection();
    $sql = "
            CREATE TABLE IF NOT EXISTS tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT,
                due_until DATETIME,
                done INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
    $pdo->exec($sql);

    self::seed($pdo);
  }

  public static function down()
  {
    $pdo = Database::getConnection();
    $pdo->exec("DROP TABLE IF EXISTS tasks;");
  }

  /**
   * Inserts sample data into the tasks table.
   *
   * @param PDO $pdo
   * @return void
   */
  private static function seed(PDO $pdo): void
  {
    $stmt = $pdo->prepare("
      INSERT INTO tasks (title, description, due_until, done)
      VALUES 
        (?, ?, ?, ?),
        (?, ?, ?, ?),
        (?, ?, ?, ?)
    ");

    $stmt->execute([
      'Buy groceries',
      'Milk, eggs, bread',
      '2025-07-10 17:00:00',
      0,
      'Call John',
      'Discuss project deadline',
      '2025-07-05 14:00:00',
      1,
      'Clean the house',
      null,
      '2025-07-07 09:00:00',
      0
    ]);
  }
}
