document.addEventListener("DOMContentLoaded", () => {
  const filterSelect = document.getElementById("filterSelect");
  const sortSelect = document.getElementById("sortSelect");
  const taskList = document.getElementById("taskList");

  if (!taskList) return;

  function filterTasks() {
    const filterValue = filterSelect?.value || "all";
    const tasks = taskList.querySelectorAll(".task-item");

    tasks.forEach((task) => {
      const isDone = task.classList.contains("done");

      const shouldShow =
        filterValue === "all" ||
        (filterValue === "completed" && isDone) ||
        (filterValue === "incomplete" && !isDone);

      task.style.display = shouldShow ? "" : "none";
    });
  }

  function sortTasks() {
    const sortValue = sortSelect?.value || "none";
    const tasksArray = Array.from(taskList.querySelectorAll(".task-item"));

    const getDueDate = (task) =>
      new Date(
        task.querySelector(".task-due")?.textContent.replace("🕓 Due: ", "") ||
          ""
      ).getTime();

    const getTitle = (task) =>
      task.querySelector(".task-title")?.textContent.trim().toLowerCase() || "";

    if (sortValue === "due") {
      tasksArray.sort((a, b) => getDueDate(a) - getDueDate(b));
    } else if (sortValue === "title") {
      tasksArray.sort((a, b) => getTitle(a).localeCompare(getTitle(b)));
    }

    tasksArray.forEach((task) => taskList.appendChild(task));
  }

  filterSelect?.addEventListener("change", filterTasks);
  sortSelect?.addEventListener("change", sortTasks);

  sortSelect?.addEventListener("change", filterTasks);
});
