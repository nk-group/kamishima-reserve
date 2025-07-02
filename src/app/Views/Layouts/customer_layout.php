<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?? 'ja' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <title><?= $this->renderSection('title', 'Clear車検予約 | 上嶋自動車') ?></title>

    <?php // Vite 顧客向けアセットの読み込み ?>
    <?= vite_tags(['assets/scss/customer/customer.scss', 'assets/js/customer.js']) ?>

    <?= $this->renderSection('page_specific_head') ?>
</head>
<body id="<?= esc($body_id ?? 'page-customer-default') ?>" class="<?= ($is_iframe ?? false) ? 'iframe-mode' : 'standalone-mode' ?>">

    <?php // iframe以外の場合はヘッダーを表示 ?>
    <?php if (!($is_iframe ?? false)): ?>
        <?= $this->include('Partials/_customer_header') ?>
    <?php endif; ?>

    <div class="main-content">
        <?= $this->renderSection('page_header_content') ?>
        <?= $this->renderSection('content') ?>
    </div>

    <?php // iframe以外の場合はフッターを表示 ?>
    <?php if (!($is_iframe ?? false)): ?>
        <?= $this->include('Partials/_customer_footer') ?>
    <?php endif; ?>

    <?= $this->renderSection('page_specific_scripts') ?>
</body>
</html>