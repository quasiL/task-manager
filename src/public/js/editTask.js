import { escapeHtml } from "./helpers.js";
import { formatForInput } from "./helpers.js";

document.addEventListener("click", function (e) {
  if (e.target.classList.contains("edit-btn")) {
    const li = e.target.closest(".task-item");
    const id = li.dataset.id;

    const titleEl = li.querySelector(".task-title");
    const descEl = li.querySelector(".task-desc");
    const dueEl = li.querySelector(".task-due");

    if (e.target.textContent.includes("Edit")) {
      const title = titleEl.textContent;
      const description = descEl.textContent;
      const dueMatch = dueEl.textContent.match(/Due: (.*)/);
      const dueUntil = dueMatch ? dueMatch[1] : "";

      const done = li.classList.contains("done");

      titleEl.innerHTML = `<input type="text" class="edit-title" value="${escapeHtml(
        title
      )}" required />`;
      descEl.innerHTML = `<textarea class="edit-desc">${escapeHtml(
        description
      )}</textarea>`;
      dueEl.innerHTML = `🕓 Due: <input type="datetime-local" class="edit-due" value="${formatForInput(
        dueUntil
      )}" />`;

      if (!li.querySelector(".edit-done-checkbox")) {
        const doneCheckbox = document.createElement("label");
        doneCheckbox.className = "edit-done-checkbox";
        doneCheckbox.style.display = "block";
        doneCheckbox.style.marginTop = "0.5rem";
        doneCheckbox.innerHTML = `
          <input type="checkbox" class="edit-done-input" ${
            done ? "checked" : ""
          } /> Done
        `;
        dueEl.after(doneCheckbox);
      }

      e.target.textContent = "💾 Save";
    } else {
      const newTitle = li.querySelector(".edit-title").value;
      const newDesc = li.querySelector(".edit-desc").value;
      const newDue = li.querySelector(".edit-due").value;
      const newDone = li.querySelector(".edit-done-input").checked ? 1 : 0;

      const messageDiv = document.getElementById("message");
      if (!newTitle) {
        messageDiv.style.color = "red";
        messageDiv.textContent = "Title is required.";
        return;
      }

      if (newDue) {
        const dueDate = new Date(newDue);
        const now = new Date();
        if (dueDate < now) {
          messageDiv.style.color = "red";
          messageDiv.textContent = "Due date must be in the future.";
          return;
        }
      }

      fetch(`?action=edit&id=${id}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
          title: newTitle,
          description: newDesc,
          due_until: newDue,
          done: newDone,
        }),
      })
        .then((res) => res.json())
        .then((result) => {
          if (result.success) {
            titleEl.textContent = newTitle;
            descEl.textContent = newDesc || "No description";
            dueEl.textContent = `🕓 Due: ${newDue.replace("T", " ")}`;

            const doneCheckboxLabel = li.querySelector(".edit-done-checkbox");
            if (doneCheckboxLabel) doneCheckboxLabel.remove();

            if (newDone) {
              li.classList.add("done");
            } else {
              li.classList.remove("done");
            }

            e.target.textContent = "✏️ Edit";
          } else {
            console.error(result.error || "Failed to update task.");
          }
        })
        .catch((err) => console.error("Error updating task: " + err.message));
    }
  }
});
