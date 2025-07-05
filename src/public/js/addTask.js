import { escapeHtml } from "./helpers.js";

document
  .getElementById("taskForm")
  ?.addEventListener("submit", async function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const messageDiv = document.getElementById("message");
    const container = document.getElementById("tasksContainer");

    const title = formData.get("title")?.trim();
    const dueUntil = formData.get("due_until");

    if (!title) {
      messageDiv.style.color = "red";
      messageDiv.textContent = "Title is required.";
      return;
    }

    if (dueUntil) {
      const dueDate = new Date(dueUntil);
      const now = new Date();
      if (dueDate < now) {
        messageDiv.style.color = "red";
        messageDiv.textContent = "Due date must be in the future.";
        return;
      }
    }

    try {
      const response = await fetch("?action=add", {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });

      const result = await response.json();

      if (result.success) {
        messageDiv.style.color = "green";
        messageDiv.textContent = "Task added successfully!";
        form.reset();

        const noTasksMsg = container.querySelector(".no-tasks");
        if (noTasksMsg) noTasksMsg.remove();

        let taskList = document.getElementById("taskList");

        if (!taskList) {
          taskList = document.createElement("ul");
          taskList.id = "taskList";
          taskList.classList.add("task-list");
          container.appendChild(taskList);
        }

        const li = document.createElement("li");
        li.classList.add("task-item");
        if (result.task.done) li.classList.add("done");
        li.setAttribute("data-id", result.task.id);

        li.innerHTML = `
          <div class="task-header">
            <span class="task-title">${escapeHtml(result.task.title)}</span>
            ${
              result.task.done
                ? '<span class="task-status">✅ Completed</span>'
                : ""
            }
            <button class="edit-btn">✏️ Edit</button>
            <button class="delete-btn" data-id="${result.task.id}">🗑️</button>
          </div>
          <div class="task-desc">${escapeHtml(
            result.task.description || "No description"
          )}</div>
          <div class="task-due">🕓 Due: ${escapeHtml(
            result.task.dueUntil || "N/A"
          )}</div>
        `;

        taskList.prepend(li);
      } else {
        messageDiv.style.color = "red";
        messageDiv.textContent = result.error || "Failed to add task";
      }
    } catch (err) {
      messageDiv.style.color = "red";
      messageDiv.textContent = "Error: " + err.message;
    }
  });
