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
