<h1>🗒️ Task List</h1>

<form id="taskForm">
  <input type="text" name="title" placeholder="Task title" required />
  <textarea name="description" placeholder="Description"></textarea>
  <label>Due Until: <input type="datetime-local" name="due_until"></label>
  <label><input type="checkbox" name="done"> Done</label>
  <button type="submit">Add Task</button>
</form>

<div id="message"></div>

<hr>

<div id="taskControls" style="margin-bottom: 1rem; display: flex; gap: 1rem; align-items: center;">
  <div id="taskFilter">
    <label for="filterSelect"><strong>Filter:</strong></label>
    <select id="filterSelect">
      <option value="all">All</option>
      <option value="completed">Completed</option>
      <option value="incomplete">Incomplete</option>
    </select>
  </div>
  <div id="taskSort">
    <label for="sortSelect"><strong>Sort by:</strong></label>
    <select id="sortSelect">
      <option value="none">None</option>
      <option value="due">Due date</option>
      <option value="title">Title</option>
    </select>
  </div>
</div>

<div id="tasksContainer">
  <?php if (!empty($tasks)): ?>
    <ul id="taskList" class="task-list">
      <?php foreach ($tasks as $task): ?>
        <li class="task-item <?= $task->done ? 'done' : '' ?>" data-id="<?= $task->id ?>">
          <div class="task-header">
            <span class="task-title"><?= htmlspecialchars($task->title) ?></span>
            <button class="toggle-done-btn" data-id="<?= $task->id ?>">
              ✅ Completed
            </button>
            <button class="edit-btn">✏️ Edit</button>
            <button class="delete-btn" data-id="<?= $task->id ?>">🗑️</button>
          </div>
          <div class="task-desc"><?= htmlspecialchars($task->description ?? 'No description') ?></div>
          <div class="task-due">🕓 Due: <?= htmlspecialchars($task->dueUntil ?? 'N/A') ?></div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p class="no-tasks">No tasks found.</p>
  <?php endif; ?>
</div>

<script type="module" src="/js/addTask.js"></script>
<script type="module" src="/js/deleteTask.js"></script>
<script type="module" src="/js/editTask.js"></script>
<script type="module" src="/js/taskControls.js"></script>
<script type="module" src="/js/toggleDone.js"></script>