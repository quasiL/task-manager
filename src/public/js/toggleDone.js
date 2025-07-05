document.addEventListener("DOMContentLoaded", () => {
  const taskList = document.getElementById("taskList");

  taskList?.addEventListener("click", async (e) => {
    const btn = e.target.closest(".toggle-done-btn");
    if (!btn) return;

    const taskId = btn.dataset.id;
    if (!taskId) return;

    try {
      const response = await fetch(`?action=toggle_done&id=${taskId}`, {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });

      const result = await response.json();

      if (result.success) {
        const taskItem = btn.closest(".task-item");
        taskItem?.classList.toggle("done", result.task.done);
      } else {
        console.error(result.error || "Failed to update task status");
      }
    } catch (err) {
      console.error("Error toggling task status: " + err.message);
    }
  });
});
