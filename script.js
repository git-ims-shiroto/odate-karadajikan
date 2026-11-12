(() => {
  const content = window.LP_CONTENT;
  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
  const escapeHtml = (value) => String(value).replace(/[&<>'"]/g, (char) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", "'": "&#39;", '"': "&quot;" })[char]);
  const renderResource = (item) => item.videoId ? `
    <div class="video-embed"><iframe src="https://www.youtube-nocookie.com/embed/${escapeHtml(item.videoId)}" title="${escapeHtml(item.name)}の紹介動画" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>
  ` : item.modalUrl ? `<button class="method-link method-popup" type="button" data-site-modal data-site-url="${escapeHtml(item.modalUrl)}" data-site-title="${escapeHtml(item.linkLabel)}">${escapeHtml(item.linkLabel)} →</button>` : item.url ? `<a class="method-link" href="${escapeHtml(item.url)}" target="_blank" rel="noopener noreferrer">${escapeHtml(item.linkLabel)} →</a>` : "";

  document.title = `${content.siteName}｜大館市のやさしい体操教室`;
  $("#brandName").textContent = content.siteName;

  $("#concerns").innerHTML = content.concerns.map((item) => `
    <article class="concern-card"><span class="concern-icon" aria-hidden="true">${escapeHtml(item.icon)}</span><p>${escapeHtml(item.title)}</p></article>
  `).join("");

  const symbols = ["貸", "円", "楽"];
  $("#reasons").innerHTML = content.reasons.map((item, index) => `
    <article class="reason-card"><span class="reason-number">${escapeHtml(item.number)}</span><span class="reason-symbol" aria-hidden="true">${symbols[index]}</span><h3>${escapeHtml(item.title)}</h3><p>${escapeHtml(item.text)}</p></article>
  `).join("");

  $("#methods").innerHTML = content.methods.map((item) => `
    <article class="method-card"><div class="method-image"><img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.alt)}" /><span class="image-note">モック用イメージ</span></div><div><h4>${escapeHtml(item.name)}</h4><p>${escapeHtml(item.text)}</p>${renderResource(item)}</div></article>
  `).join("");

  $("#saturdayMethods").innerHTML = content.saturdayMethods.map((item) => `
    <div class="saturday-method"><b>${escapeHtml(item.name)}</b><span>${escapeHtml(item.text)}</span>${renderResource(item)}</div>
  `).join("");

  const siteModal = $("#siteModal");
  const siteModalFrame = $("#siteModalFrame");
  const siteModalTitle = $("#siteModalTitle");
  const siteModalExternal = $("#siteModalExternal");
  const closeSiteModal = () => {
    if (typeof siteModal.close === "function") siteModal.close();
    else siteModal.removeAttribute("open");
  };
  $$('[data-site-modal]').forEach((button) => button.addEventListener("click", () => {
    const url = button.dataset.siteUrl;
    const title = button.dataset.siteTitle;
    siteModalTitle.textContent = title;
    siteModalFrame.title = title;
    siteModalFrame.src = url;
    siteModalExternal.href = url;
    document.body.classList.add("modal-open");
    if (typeof siteModal.showModal === "function") siteModal.showModal();
    else siteModal.setAttribute("open", "");
  }));
  $("#siteModalClose").addEventListener("click", closeSiteModal);
  siteModal.addEventListener("click", (event) => {
    if (event.target === siteModal) closeSiteModal();
  });
  siteModal.addEventListener("close", () => {
    siteModalFrame.src = "about:blank";
    document.body.classList.remove("modal-open");
  });

  // 参加者の声：最初の3件を表示し、残りは「もっと見る」のドロワーで全件を表示する
  const VOICE_LIMIT = 3;
  const voiceInitial = (age) => String(age).split(" ").pop().charAt(0);
  const voiceCard = (item) => `
    <article class="voice-card"><div class="voice-person"><span class="voice-avatar" aria-hidden="true">${escapeHtml(voiceInitial(item.age))}</span><div><b>${escapeHtml(item.age)}</b><span>${escapeHtml(item.tag)}</span></div></div><blockquote>${escapeHtml(item.quote)}</blockquote></article>
  `;
  $("#voicesGrid").innerHTML = content.voices.slice(0, VOICE_LIMIT).map(voiceCard).join("");
  const voiceDrawer = $("#voiceDrawer");
  if (content.voices.length > VOICE_LIMIT) {
    $("#voiceDrawerList").innerHTML = content.voices.map(voiceCard).join("");
    $("#voiceMoreCount").textContent = `（全${content.voices.length}件）`;
    $("#voiceMore").hidden = false;
    $("#voiceMoreButton").addEventListener("click", () => {
      document.body.classList.add("modal-open");
      voiceDrawer.showModal();
    });
    $("#voiceDrawerClose").addEventListener("click", () => voiceDrawer.close());
    voiceDrawer.addEventListener("click", (event) => { if (event.target === voiceDrawer) voiceDrawer.close(); });
    voiceDrawer.addEventListener("close", () => document.body.classList.remove("modal-open"));
  }

  $("#qualificationsList").innerHTML = content.qualifications.map((item) => `
    <div class="qualification-row"><b>${escapeHtml(item.org)}</b><span>${escapeHtml(item.items)}</span></div>
  `).join("");

  $("#scheduleTabs").innerHTML = content.schedules.map((group, index) => `
    <button class="schedule-tab" type="button" role="tab" id="tab-${index}" aria-controls="panel-${index}" aria-selected="${index === 0}">${escapeHtml(group.day)}</button>
  `).join("");
  $("#schedulePanels").innerHTML = content.schedules.map((group, index) => `
    <div class="schedule-panel${index === 0 ? " active" : ""}" id="panel-${index}" role="tabpanel" aria-labelledby="tab-${index}" ${index === 0 ? "" : "hidden"}>
      ${group.classes.map((item) => `<article class="class-row"><div class="class-time">${escapeHtml(item.time)}<small>${escapeHtml(item.venue)}</small></div><div><h3>${escapeHtml(item.name)}</h3><p>${escapeHtml(item.text)}</p></div></article>`).join("")}
    </div>
  `).join("");

  $$(".schedule-tab").forEach((tab, index) => tab.addEventListener("click", () => {
    $$(".schedule-tab").forEach((button) => button.setAttribute("aria-selected", "false"));
    $$(".schedule-panel").forEach((panel) => { panel.classList.remove("active"); panel.hidden = true; });
    tab.setAttribute("aria-selected", "true");
    const panel = $(`#panel-${index}`);
    panel.hidden = false;
    panel.classList.add("active");
  }));

  $("#venues").innerHTML = content.venues.map((item) => {
    const query = encodeURIComponent(item.address);
    return `<article class="venue-card"><h3>${escapeHtml(item.name)}${item.note ? `<small>${escapeHtml(item.note)}</small>` : ""}</h3><p>${escapeHtml(item.address)}<br />TEL ${escapeHtml(item.phone)}</p><a href="https://www.google.com/maps/search/?api=1&query=${query}" target="_blank" rel="noopener noreferrer">Googleマップで見る →</a><span class="parking">${escapeHtml(item.parking)}</span></article>`;
  }).join("");

  $("#faqList").innerHTML = content.faqs.map((item, index) => `
    <div class="faq-item${index === 0 ? " open" : ""}"><button type="button" aria-expanded="${index === 0}" aria-controls="answer-${index}"><span class="faq-q" aria-hidden="true">Q</span><span>${escapeHtml(item.q)}</span><span class="faq-toggle" aria-hidden="true">＋</span></button><div class="faq-answer" id="answer-${index}">${escapeHtml(item.a)}</div></div>
  `).join("");
  $$(".faq-item button").forEach((button) => button.addEventListener("click", () => {
    const item = button.closest(".faq-item");
    const willOpen = !item.classList.contains("open");
    item.classList.toggle("open", willOpen);
    button.setAttribute("aria-expanded", String(willOpen));
  }));

  const toast = $("#toast");
  $$('[data-mock-line]').forEach((link) => link.addEventListener("click", (event) => {
    event.preventDefault();
    toast.classList.add("show");
    window.setTimeout(() => toast.classList.remove("show"), 2400);
  }));
})();
