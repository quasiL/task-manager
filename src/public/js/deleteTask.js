document.addEventListener("click", async function (e) {
  if (e.target.classList.contains("delete-btn")) {
    const taskId = e.target.dataset.id;

    try {
      const response = await fetch(`?action=delete&id=${taskId}`, {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });

      const result = await response.json();
      if (result.success) {
        const taskItem = e.target.closest(".task-item");
        if (taskItem) taskItem.remove();
      } else {
        alert(result.error || "Failed to delete task.");
      }
    } catch (err) {
      alert("Error deleting task: " + err.message);
    }
  }
});
