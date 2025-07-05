export function escapeHtml(text) {
  return text.replace(/[&<>"']/g, function (m) {
    return {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#39;",
    }[m];
  });
}

export function formatForInput(datetimeStr) {
  return datetimeStr.trim().replace(" ", "T").slice(0, 16);
}
