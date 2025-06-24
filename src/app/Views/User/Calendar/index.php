<?= $this->extend('Layouts/user_layout') // 利用者向け共通レイアウトを継承 ?>

<?= $this->section('page_specific_assets') // user-layout.php のセクションに対応 ?>
  <?php
    // カレンダーページ専用のCSSを読み込む
    if (function_exists('vite_tags')) {
        echo vite_tags('assets/css/calendar-page.scss');
    }
  ?>
<?= $this->endSection() ?>

<?= $this->section('content') // user-layout.php の renderSection('content') に対応 ?>

<h2>予約状況確認カレンダー</h2>
<p>こちらのページで車検の予約状況をご確認いただけます。</p>

<div id="reservation-calendar-container">
  <p style="padding:20px; border:1px dashed #ccc; text-align:center;">
    カレンダー表示エリア
  </p>
</div>

<?php
// カレンダー表示に必要なJavaScriptをここで読み込むか、
// user.js でグローバルに初期化するかなどを検討します。
// 例: Flatpickr や FullCalendar などのライブラリを使用
?>
<?= $this->endSection() ?>