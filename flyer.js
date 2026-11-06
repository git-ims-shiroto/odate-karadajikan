(() => {
  const content = window.LP_CONTENT;
  const scheduleRoot = document.querySelector("#flyerSchedules");
  const escapeHtml = (value) => String(value).replace(/[&<>'"]/g, (char) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", "'": "&#39;", '"': "&quot;" })[char]);

  document.querySelector("#flyerSiteName").textContent = content.siteName;

  scheduleRoot.innerHTML = content.schedules.map((group) => `
    <article class="schedule-day">
      <h3>${escapeHtml(group.day)}<small>${group.classes.length}クラス</small></h3>
      ${group.classes.map((item) => `
        <div class="flyer-class">
          <time>${escapeHtml(item.time)}</time>
          <b>${escapeHtml(item.name)}</b>
          <span>${escapeHtml(item.venue)}</span>
          <p>${escapeHtml(item.text)}</p>
          ${group.day === "土曜" ? '<span class="reserve-badge">要予約</span>' : ""}
        </div>
      `).join("")}
    </article>
  `).join("");

  document.querySelector("#flyerVenues").innerHTML = content.venues.map((venue) => `
    <p><b>${escapeHtml(venue.name)}</b>${escapeHtml(venue.address)}</p>
  `).join("");

  document.querySelector("#printFlyer").addEventListener("click", () => window.print());
})();
