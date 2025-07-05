<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Task;
use App\Database\Database;
use PDO;

class TaskRepository
{
  /**
   * Returns all tasks
   * 
   * @return Task[]
   */
  public function all(): array
  {
    $pdo = Database::getConnection();

    if (!Database::tableExists('tasks')) {
      return [];
    }

    $stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(fn($row) => Task::fromArray($row), $rows);
  }

  /**
   * Creates a new task
   * 
   * @param array $data
   * @return void
   */
  public function create(array $data): void
  {
    $pdo = Database::getConnection();

    if (!Database::tableExists('tasks')) {
      return;
    }

    $task = Task::fromArray($data);

    $stmt = $pdo->prepare("
        INSERT INTO tasks (title, description, due_until, done)
        VALUES (:title, :description, :due_until, :done)
    ");

    $stmt->execute([
      ':title' => $task->title,
      ':description' => $task->description,
      ':due_until' => $task->dueUntil,
      ':done' => $task->done,
    ]);
  }

  /**
   * Deletes a task
   * 
   * @param int $id
   * @return bool Returns true if the operation was successful false otherwise
   */
  public function delete(int $id): bool
  {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    return $stmt->execute([$id]);
  }

  /**
   * Updates an existing task
   * 
   * @param int $id
   * @param array $data
   * @return bool Returns true if the operation was successful false otherwise
   */
  public function update(int $id, array $data): bool
  {
    $pdo = Database::getConnection();

    $task = Task::fromArray($data);

    $stmt = $pdo->prepare("
          UPDATE tasks
          SET title = :title,
              description = :description,
              due_until = :due_until,
              done = :done
          WHERE id = :id
      ");

    return $stmt->execute([
      ':title' => $task->title,
      ':description' => $task->description,
      ':due_until' => $task->dueUntil,
      ':done' => $task->done,
      ':id' => $id
    ]);
  }

  /**
   * Updates the done status of a task
   * 
   * @param int $id
   * @param bool $done
   * @return bool Returns true if the operation was successful false otherwise
   */
  public function updateDoneStatus(int $id, bool $done): bool
  {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("UPDATE tasks SET done = :done WHERE id = :id");

    return $stmt->execute([
      ':done' => $done ? 1 : 0,
      ':id' => $id,
    ]);
  }
}
