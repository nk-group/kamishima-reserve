<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
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
<body id="<?= esc($body_id ?? '') ?>">
    <?php // ページ固有の<body>開始直後コンテンツ (必要に応じて各ビューで section('page_specific_after_body_start') を定義) ?>
    <?= $this->renderSection('page_specific_after_body_start') ?>

    <header class="admin-header navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= site_url('admin/dashboard') ?>">
                <img src="<?= base_url('images/logo.png') ?>" alt="ロゴ">
                車検予約管理システム
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbarContent" aria-controls="adminNavbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= (current_url() == site_url('admin/dashboard')) ? 'active' : '' ?>" aria-current="page" href="<?= site_url('admin/dashboard') ?>"><i class="bi bi-house-door-fill me-1"></i>HOME</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="reservationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-calendar-plus-fill me-1"></i>予約管理
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="reservationsDropdown">
                            <li><a class="dropdown-item" href="<?= site_url('admin/reservations/new') // 要実装 ?>"><i class="bi bi-plus-circle me-2"></i>新規予約</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('admin/reservations') // 要実装 ?>"><i class="bi bi-search me-2"></i>予約検索／一覧</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('admin/reservations/calendar') // 要実装 ?>"><i class="bi bi-calendar-week me-2"></i>スケジュールカレンダー</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="maintenanceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-tools me-1"></i>メンテナンス
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="maintenanceDropdown">
                            <li><a class="dropdown-item" href="<?= site_url('admin/holidays') // 要実装 ?>"><i class="bi bi-calendar-x me-2"></i>休日管理</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-envelope-check me-2"></i>リマインドメール状況</a></li>
                            <?php if (auth()->user() && auth()->user()->inGroup('admin')): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">管理者用メニュー</h6></li>
                            <li><a class="dropdown-item" href="<?= site_url(route_to('admin.users.index')) ?>"><i class="bi bi-people-fill me-2"></i>ユーザー管理</a></li>
                            <li><a class="dropdown-item" href="<?= site_url(route_to('admin.vehicle-types.index')) ?>"><i class="bi bi-truck-front-fill me-2"></i>車両種別マスタ</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('admin/data/delete') // 要実装 ?>"><i class="bi bi-trash2-fill me-2"></i>データ一括削除</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('admin/settings/reminders') // 要実装 ?>"><i class="bi bi-envelope-paper-fill me-2"></i>リマインドメール設定</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('admin/settings/system') // 要実装 ?>"><i class="bi bi-gear-fill me-2"></i>動作環境設定</a></li>
                                <?php if (ENVIRONMENT === 'development'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= site_url('dev-test') ?>"><i class="bi bi-bug-fill me-2"></i>※デバッグページ※</a></li>
                                <?php endif; ?>
                            <?php endif; ?>
                        </ul>
                    </li>
                </ul>
                <?php if (auth()->loggedIn()): ?>
                <div class="d-flex align-items-center text-white">
                    <span class="navbar-text me-3">
                        ようこそ、<?= esc(auth()->user()->full_name ?? (auth()->user()->username ?? (auth()->user()->email ?? 'ゲスト'))) ?> さん
                    </span>
                    <a href="<?= site_url('logout') ?>" class="btn btn-outline-light">ログアウト</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="admin-body">
        <main class="admin-main-content">
            <div class="container-fluid py-4">
                <?php // ★★★ ここにメインコンテンツが表示されます ★★★ ?>
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

    <footer class="admin-footer bg-light text-center py-3 mt-auto">
        <div class="container">
            <p>&copy; <?= date('Y') ?> 車検予約管理システム</p>
        </div>
    </footer>

    <?php // ページ固有の</body>直前スクリプト (必要に応じて各ビューで section('page_specific_before_body_end') を定義) ?>
    <?= $this->renderSection('page_specific_before_body_end') ?>
</body>
</html>