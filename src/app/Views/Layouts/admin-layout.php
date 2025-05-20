<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title ?? '管理画面 | 車検予約管理システム') ?></title>
    <?php
        // --- Vite アセットの読み込み ---
        // 管理者向けアセット ('assets/js/app.js' をエントリーポイントとして指定)
        if (function_exists('vite_tags')) {
            echo vite_tags('assets/js/app.js');
        } else {
            echo "";
        }
    ?>
    <?php // ページ固有の<head>内アセット (必要に応じて各ビューで section('page_specific_before_head_end') を定義) ?>
    <?= $this->renderSection('page_specific_before_head_end') ?>
</head>
<body>
    <?php // ページ固有の<body>開始直後コンテンツ (必要に応じて各ビューで section('page_specific_after_body_start') を定義) ?>
    <?= $this->renderSection('page_specific_after_body_start') ?>

    <header class="admin-header">
        <div class="container">
            <h1><a href="<?= site_url('admin/dashboard') ?>">車検予約管理システム</a></h1>
            <?php if (auth()->loggedIn()): ?>
            <nav class="user-nav">
                <span>ようこそ、<?= esc(auth()->user()->full_name ?? (auth()->user()->username ?? (auth()->user()->email ?? 'ゲスト'))) ?> さん</span>
                <a href="<?= site_url('logout') ?>">ログアウト</a>
            </nav>
            <?php endif; ?>
        </div>
    </header>

    <div class="admin-body">
        <?php if (auth()->loggedIn()): ?>
        <aside class="admin-navigation">
            <nav>
                <ul>
                    <li><a href="<?= site_url('admin/dashboard') ?>">ダッシュボード</a></li>
                    <li><a href="<?= site_url('admin/reservations/calendar') // 要実装 ?>">予約状況カレンダー</a></li>
                    <li><a href="<?= site_url('admin/reservations') // 要実装 ?>">予約検索／一覧</a></li>
                    <li><a href="<?= site_url('admin/holidays') // 要実装 ?>">定休日管理</a></li>
                    <?php if (auth()->user() && auth()->user()->inGroup('admin')): ?>
                    <li class="menu-separator">--- 管理者メニュー ---</li>
                    <li><a href="<?= site_url('admin/users') // 今回作成したユーザー一覧へのリンク ?>">ユーザーマスタ</a></li>
                    <li><a href="<?= site_url('admin/vehicle-types') ?>">車両種別マスタ</a></li>
                    <li><a href="<?= site_url(route_to('admin.users.index')) ?>">ユーザーマスタ</a></li>
                    <li><a href="<?= site_url(route_to('admin.vehicle-types.index')) ?>">車両種別マスタ</a></li>                    
                    <li><a href="<?= site_url('admin/data/delete') // 要実装 ?>">データ一括削除</a></li>
                    <li><a href="<?= site_url('admin/settings/reminders') // 要実装 ?>">リマインド設定</a></li>
                    <li><a href="<?= site_url('admin/settings/system') // 要実装 ?>">動作環境設定</a></li>

                    <?php if (ENVIRONMENT === 'development'): ?>
                    <li><a href="<?= site_url('dev-test') ?>">※デバッグページ※</a></li>
                    <?php endif; ?>

                    <?php endif; ?>
                </ul>
            </nav>
        </aside>
        <?php endif; ?>

        <main class="admin-main-content">
            <div class="container">
                <?php // ★★★ ここにメインコンテンツが表示されます ★★★ ?>
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

    <footer class="admin-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> 車検予約管理システム</p>
        </div>
    </footer>

    <?php // ページ固有の</body>直前スクリプト (必要に応じて各ビューで section('page_specific_before_body_end') を定義) ?>
    <?= $this->renderSection('page_specific_before_body_end') ?>
</body>
</html>