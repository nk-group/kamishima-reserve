<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?? 'ja' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <title><?= $this->renderSection('title', '管理画面 | 車検予約管理システム') ?></title>

    <?php // Vite アセットの読み込み (新しいエントリーポイントを指定) ?>
    <?= vite_tags(['assets/scss/admin/admin.scss', 'assets/js/admin.js']) ?>

    <?= $this->renderSection('page_specific_head') ?>
</head>
<body id="<?= esc($body_id ?? '') ?>">

    <?= $this->include('Partials/_admin_header') // 新しいヘッダーパーシャルを読み込み (ファイル名変更) ?>

    <div class="main-content"> <?php // mainタグをdivに変更 ?>
        <?= $this->renderSection('page_header_content') // ページヘッダー用セクション (page-headerクラスを持つdivを想定) ?>
        <?= $this->renderSection('content') // 各ページのメインコンテンツ (例: dashboard-content クラスを持つdiv) ?>
    </div>

    <?= $this->include('Partials/_admin_footer') // 新しいフッターパーシャルを読み込み (ファイル名変更) ?>

    <?= $this->renderSection('page_specific_scripts') ?>
</body>
</html>
