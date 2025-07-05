<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\TaskRepository;

class TaskController extends BaseController
{
  private readonly TaskRepository $taskRepository;

  public function __construct()
  {
    $this->taskRepository = new TaskRepository();
  }

  /**
   * Handles the request and performs the appropriate action
   * 
   * @return void
   */
  public function handleRequest(): void
  {
    $action = htmlspecialchars($_GET['action'] ?? '');

    switch ($action) {
      case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $this->addNewTask();
        }
        break;
      case 'delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $this->deleteTask();
        }
        break;
      case 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $this->updateTask();
        }
        break;
      case 'toggle_done':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $this->toggleDoneStatus();
        }
        break;
      default:
        $tasks = $this->taskRepository->all();
        $this->render('task_list', ['tasks' => $tasks]);
        break;
    }
  }

  private function addNewTask(): void
  {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    $title = htmlspecialchars($_POST['title']) ?? '';
    $description = htmlspecialchars($_POST['description']) ?? null;
    $dueUntil = htmlspecialchars($_POST['due_until']) ?? null;
    $done = isset($_POST['done']) ? 1 : 0;

    if ($dueUntil) {
      $dueUntil = date('Y-m-d H:i:s', strtotime($dueUntil));
    }

    if (trim($title) === '') {
      if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Title is required']);
        return;
      }
      $this->render('task_list', ['error' => 'Title is required', 'tasks' => $this->taskRepository->all()]);
      return;
    }

    $this->taskRepository->create([
      'title' => $title,
      'description' => $description,
      'due_until' => $dueUntil,
      'done' => $done,
    ]);

    if ($isAjax) {
      header('Content-Type: application/json');
      echo json_encode([
        'success' => true,
        'task' => [
          'title' => $title,
          'description' => $description,
          'dueUntil' => $dueUntil,
          'done' => (bool)$done,
        ]
      ]);
    }
  }

  private function deleteTask(): void
  {
    $id = (int) (htmlspecialchars($_GET['id']) ?? 0);

    if ($id) {
      $deleted = $this->taskRepository->delete($id);
      echo json_encode(['success' => $deleted]);
    } else {
      echo json_encode(['success' => false, 'error' => 'Invalid task ID']);
    }
  }

  private function updateTask(): void
  {
    $id = (int) (htmlspecialchars($_GET['id']) ?? 0);

    if (!$id) {
      echo json_encode(['success' => false, 'error' => 'Invalid ID']);
      return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['title'])) {
      echo json_encode(['success' => false, 'error' => 'Missing data']);
      return;
    }

    $dueUntil = $data['due_until'] ?? null;
    if ($dueUntil) {
      $dueUntil = date('Y-m-d H:i:s', strtotime($dueUntil));
    }

    $updated = $this->taskRepository->update($id, [
      'title' => trim($data['title']),
      'description' => trim($data['description'] ?? ''),
      'due_until' => $dueUntil,
      'done' => filter_var(
        $data['done'] ?? null,
        FILTER_VALIDATE_BOOLEAN,
        FILTER_NULL_ON_FAILURE
      ),
    ]);

    echo json_encode(['success' => $updated]);
  }

  private function toggleDoneStatus(): void
  {
    $id = (int) (htmlspecialchars($_GET['id']) ?? 0);
    $done = filter_var($_POST['done'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

    if (!$id || $done === null) {
      echo json_encode(['success' => false, 'error' => 'Invalid input']);
      return;
    }

    $newStatus = !$done;
    $success = $this->taskRepository->updateDoneStatus($id, $newStatus);

    echo json_encode([
      'success' => $success,
      'task' => ['id' => $id, 'done' => $newStatus],
    ]);
  }
}
