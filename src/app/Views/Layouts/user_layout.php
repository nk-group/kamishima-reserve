<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($page_title ?? '車検予約システム') // 利用者向けページ共通タイトル ?></title>
  <?php
    // 利用者向けアセットを読み込む (vite.config.js の inputキー 'user_app' に対応するエントリー)
    // 'assets/js/user.js' を渡す (vite_tags() ヘルパーはプロジェクトルートからのパスを期待)
    echo vite_tags(['assets/scss/customer/customer.scss', 'assets/js/customer.js']);
  ?>
  <?= $this->renderSection('page_specific_before_head_end') // ★ページ固有のコード ?>
</head>

<body class="user-page-body">
  <?= $this->renderSection('page_specific_after_body_start') // ★ページ固有のコード ?>
  <header class="user-header">
    <div class="container"> <h1><a href="<?= site_url('/') ?>">車検予約システム</a></h1></div>
  </header>

  <main class="user-container">
    <?= $this->renderSection('content') // 各ページのメインコンテンツがここに挿入されます ?>
  </main>

  <footer class="user-footer">
    <div class="container"> <p>&copy; <?= date('Y') ?> 車検予約システム. All rights reserved.</p></div>
  </footer>
  <?= $this->renderSection('page_specific_before_body_end') // ★ページ固有のコード ?>
</body>

</html>