/* スクロールアニメーション：各セクションの見出し・カードが画面に入ったら、ふわっと表示する（1回だけ）。
   ・ファーストビュー、ポップアップ、ドロワーの中は対象外。
   ・「視差効果を減らす」設定の端末、IntersectionObserver 非対応のブラウザ、JavaScript 無効時は、最初から全部表示する。
   モック（reveal.js）とテーマ（assets/js/reveal.js）で同じ内容。 */
(function () {
  if (!('IntersectionObserver' in window)) return;
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  // まとまりごとに表示する要素
  var blocks = [
    '.section-heading', '.reassurance', '.lesson-intro', '.saturday-box', '.younger-card',
    '.instructor-layout', '.qualifications', '.price-card', '.schedule-wrap', '.faq-list', '.trial-card'
  ];
  // 横に並ぶカード（少しずつ時間をずらして表示）
  var cards = [
    '.concern-card', '.reason-card', '.method-card', '.voice-grid > .voice-card', '.venue-card'
  ];

  var targets = [];
  var add = function (el, delay) {
    if (el.closest('.hero, dialog') || el.hasAttribute('data-reveal')) return;
    el.setAttribute('data-reveal', '');
    if (delay) el.style.setProperty('--reveal-delay', delay + 's');
    targets.push(el);
  };
  blocks.forEach(function (selector) {
    document.querySelectorAll(selector).forEach(function (el) { add(el, 0); });
  });
  cards.forEach(function (selector) {
    document.querySelectorAll(selector).forEach(function (el) {
      var siblings = Array.prototype.filter.call(el.parentNode.children, function (child) { return child.matches(selector); });
      add(el, (siblings.indexOf(el) % 4) * 0.1);
    });
  });

  // 読み込み時点ですでに画面内にあるものは、隠さずにそのまま表示する（ちらつき防止）
  targets.forEach(function (el) {
    var rect = el.getBoundingClientRect();
    if (rect.top < window.innerHeight && rect.bottom > 0) el.classList.add('is-visible');
  });
  document.documentElement.classList.add('reveal-on');

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0 });
  targets.forEach(function (el) {
    if (!el.classList.contains('is-visible')) observer.observe(el);
  });
})();
