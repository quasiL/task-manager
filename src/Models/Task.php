<?php

declare(strict_types=1);

namespace App\Models;

use \InvalidArgumentException;

class Task
{
  private int $_id;
  private string $_title;
  private ?string $_description;
  private ?string $_dueUntil;
  private bool $_done;
  private string $_createdAt;

  public function __construct(
    int $id,
    string $title,
    ?string $description = null,
    ?string $dueUntil = null,
    bool $done = false,
    string $createdAt = ''
  ) {
    $this->_id = $id;
    $this->_title = $title;
    $this->_description = $description;
    $this->_dueUntil = $dueUntil;
    $this->_done = $done;
    $this->_createdAt = $createdAt;
  }

  public int $id {
    get => $this->_id;
  }

  public string $title {
    get => $this->_title;
    set(string $value) {
      if (trim($value) === '') {
        throw new InvalidArgumentException('Title cannot be empty');
      }
      $this->_title = $value;
    }
  }

  public ?string $description {
    get => $this->_description;
    set(?string $value) => $this->_description = $value;
  }

  public ?string $dueUntil {
    get => $this->_dueUntil;
    set(?string $value) => $this->_dueUntil = $value;
  }

  public bool $done {
    get => $this->_done;
    set(bool $value) => $this->_done = $value;
  }

  public string $createdAt {
    get => $this->_createdAt;
  }

  public static function fromArray(array $data): self
  {
    return new self(
      id: (int) isset($data['id']) ? $data['id'] : 0,
      title: $data['title'],
      description: $data['description'] ?? null,
      dueUntil: $data['due_until'] ?? null,
      done: (bool) $data['done'],
      createdAt: $data['created_at'] ?? ''
    );
  }
}
