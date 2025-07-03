<?php
$current_url = current_url();
$admin_dashboard_url = site_url('admin/dashboard');
$admin_reservations_url = site_url('admin/reservations');

// ★エンティティのメソッドを使用してユーザー役割を取得
$userRoleDisplay = '一般ユーザー'; // デフォルト値
if (auth()->loggedIn()) {
    $user = auth()->user();
    $userRoleDisplay = $user->getPrimaryGroupJapaneseName();
}
?>
<nav class="navbar navbar-expand-lg navbar-fusion">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $admin_dashboard_url ?>">
            <?php
            // ロゴ画像のパスは環境に合わせて調整してください。
            // 例: public/images/logo.png の場合
            $logo_path = FCPATH . 'images/logo.png'; // FCPATH は public ディレクトリを指します
            $logo_url = base_url('images/logo.png');
            // SVGロゴの代替 (dashboard.html より)
            ?>
            <img src="<?= $logo_url ?>"
                 alt="Kamishima Logo" class="logo-img"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="logo-fallback" style="display: none;">
                KAMISHIMA<br>MOTORS
            </div>
            予約管理システム
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminFusionNavbar" aria-controls="adminFusionNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminFusionNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= ($current_url == $admin_dashboard_url) ? 'active' : '' ?>" href="<?= $admin_dashboard_url ?>">HOME</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= (strpos($current_url, 'reservations') !== false) ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        予約管理
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= $admin_reservations_url ?>">予約一覧</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('admin/reservations/new') ?>">予約登録</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item"  href="#" id="arrivalSchedulePrintBtn">
                            <i class="bi bi-printer me-2"></i>
                            入庫予定表印刷
                        </a></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="bi bi-card-text me-2"></i>
                            予約カード印刷
                        </a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        メンテナンス
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= site_url(route_to('admin.shop-closing-days.index')) ?>">休日管理</a></li>
                        <li><a class="dropdown-item" href="#">リマインドメール状況</a></li>
                        <li><a class="dropdown-item" href="<?= site_url(route_to('admin.users.index')) ?>">ユーザー管理</a></li>
                        <li><a class="dropdown-item" href="#">データ一括削除</a></li>
                        <li><a class="dropdown-item" href="#">動作環境設定</a></li>
                    </ul>
                </li>
            </ul>

            <div class="user-section">
                <div class="user-info">
                    <div class="user-name"><?= esc(auth()->user()->full_name ?? auth()->user()->username ?? 'ゲスト') ?></div>
                    <div class="user-role"><?= esc($userRoleDisplay) ?></div>
                </div>
                <!-- 個人設定アイコン追加 -->
                <button type="button" class="btn btn-user-settings" id="userSettingsBtn" title="個人設定">
                    <i class="bi bi-gear-fill"></i>
                </button>
                <a href="<?= site_url('logout') ?>" class="btn btn-logout">
                    <i class="bi bi-door-open-fill logout-icon"></i>
                    ログアウト
                </a>
            </div>
        </div>
    </div>
</nav>