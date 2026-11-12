/* 参加者の声の「もっと見る」：全件を右から開くドロワーで表示する。
   dialog 非対応・JavaScript 無効のときは、ボタンを出さず先頭3件だけを表示する。 */
(function () {
  document.querySelectorAll('[data-drawer-open]').forEach(function (button) {
    var drawer = document.getElementById(button.getAttribute('data-drawer-open'));
    if (!drawer || typeof drawer.showModal !== 'function') return;
    var wrap = button.closest('[data-voice-more]');
    if (wrap) wrap.hidden = false;

    button.addEventListener('click', function () {
      document.body.classList.add('modal-open');
      drawer.showModal();
    });
    drawer.querySelectorAll('[data-drawer-close]').forEach(function (close) {
      close.addEventListener('click', function () { drawer.close(); });
    });
    drawer.addEventListener('click', function (event) {
      if (event.target === drawer) drawer.close();
    });
    drawer.addEventListener('close', function () {
      document.body.classList.remove('modal-open');
      button.focus();
    });
  });
})();
