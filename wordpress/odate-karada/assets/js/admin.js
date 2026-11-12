/* Only loaded on the LP settings screen. */
(() => {
  document.querySelectorAll('.ok-select-image').forEach(button => {
    button.addEventListener('click', () => {
      const group = button.closest('.ok-media');
      const frame = wp.media({ title: 'LPに掲載する画像を選択', button: { text: 'この画像を使う' }, library: { type: 'image' }, multiple: false });
      frame.on('select', () => {
        const attachment = frame.state().get('selection').first().toJSON();
        group.querySelector('input').value = attachment.id;
        group.querySelector('.ok-media-status').textContent = `選択：${attachment.filename}（保存すると反映）`;
        const alt = group.closest('fieldset').querySelector('textarea[name$="[alt]"]');
        if (alt && attachment.alt) alt.value = attachment.alt;
      });
      frame.open();
    });
  });
  document.querySelectorAll('.ok-remove-image').forEach(button => {
    button.addEventListener('click', () => {
      const group = button.closest('.ok-media');
      group.querySelector('input').value = 0;
      group.querySelector('.ok-media-status').textContent = '未設定（保存すると仮枠を表示）';
    });
  });
})();
/* 件数を増減できる一覧（参加者の声など）：追加・削除・並べ替え。保存時の順番は画面の並び順。 */
(() => {
  const renumber = (repeater) => {
    repeater.querySelectorAll('.ok-repeat-list > .ok-repeat-item').forEach((item, i) => {
      item.querySelector('.ok-repeat-title').textContent = `${i + 1}件目`;
    });
  };
  document.querySelectorAll('.ok-repeater').forEach((repeater) => {
    const list = repeater.querySelector('.ok-repeat-list');
    const template = repeater.querySelector('.ok-repeat-template');
    let seq = Date.now();
    repeater.querySelector('.ok-add-item').addEventListener('click', () => {
      const html = template.innerHTML.split('__INDEX__').join(String(seq++));
      list.insertAdjacentHTML('beforeend', html);
      renumber(repeater);
      const added = list.lastElementChild;
      const first = added.querySelector('textarea, input');
      if (first) first.focus();
    });
    list.addEventListener('click', (event) => {
      const button = event.target.closest('button');
      if (!button) return;
      const item = button.closest('.ok-repeat-item');
      if (button.classList.contains('ok-remove-item')) {
        if (window.confirm('この1件を削除しますか？（「LP設定を保存」で確定します）')) item.remove();
      } else if (button.classList.contains('ok-move-up') && item.previousElementSibling) {
        list.insertBefore(item, item.previousElementSibling);
      } else if (button.classList.contains('ok-move-down') && item.nextElementSibling) {
        list.insertBefore(item.nextElementSibling, item);
      } else {
        return;
      }
      renumber(repeater);
    });
  });
})();
