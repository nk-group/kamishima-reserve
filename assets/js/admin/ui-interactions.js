import { Tooltip } from 'bootstrap';

/**
 * 複数の管理ページで共通のUIインタラクションを初期化します。
 * この関数はDOMの読み込み完了後に一度だけ呼び出されるべきです。
 */
export function initAdminUIInteractions() {

  // 1. Bootstrap ツールチップの初期化
  // data-bs-toggle="tooltip" を持つすべての要素を探して有効化します。
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new Tooltip(tooltipTriggerEl);
  });

  // 2. 「LINE経由」チェックボックスと入力欄の連動機能
  const lineViaCheckbox = document.getElementById('lineVia');
  const lineDisplayNameInput = document.getElementById('lineDisplayName');

  if (lineViaCheckbox && lineDisplayNameInput) {
    const toggleLineInput = () => {
      lineDisplayNameInput.disabled = !lineViaCheckbox.checked;
    };

    // ページ読み込み時の初期状態を設定
    toggleLineInput();

    // 変更を監視するイベントリスナーを追加
    lineViaCheckbox.addEventListener('change', toggleLineInput);
  }
}