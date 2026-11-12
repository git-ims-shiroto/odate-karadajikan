/* 「ひめトレとは →」などの外部サイトをポップアップで表示する（モックと同じ動き）。
   JavaScript が無効、または dialog 非対応のときは、リンクのまま新しいタブで開く。 */
(function () {
  var modal = document.getElementById('siteModal');
  if (!modal || typeof modal.showModal !== 'function') return;
  var frame = document.getElementById('siteModalFrame');
  var title = document.getElementById('siteModalTitle');
  var external = document.getElementById('siteModalExternal');
  var closeButton = document.getElementById('siteModalClose');

  document.querySelectorAll('[data-site-modal]').forEach(function (link) {
    link.addEventListener('click', function (event) {
      event.preventDefault();
      var url = link.href;
      var label = link.getAttribute('data-site-title') || '';
      title.textContent = label;
      frame.title = label;
      frame.src = url;
      external.href = url;
      document.body.classList.add('modal-open');
      modal.showModal();
    });
  });

  closeButton.addEventListener('click', function () { modal.close(); });
  modal.addEventListener('click', function (event) {
    if (event.target === modal) modal.close();
  });
  modal.addEventListener('close', function () {
    frame.src = 'about:blank';
    document.body.classList.remove('modal-open');
  });
})();
