/**
 * Bootstrap 5 のクライアントサイドバリデーションを初期化します。
 * 'needs-validation' クラスを持つフォームに適用されます。
 */
export function initFormValidation() {
  'use strict';

  // 'needs-validation' クラスを持つすべてのフォームを取得
  const forms = document.querySelectorAll('.needs-validation');

  // 各フォームに対してループ処理
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }

        form.classList.add('was-validated');
      }, false);
    });
}