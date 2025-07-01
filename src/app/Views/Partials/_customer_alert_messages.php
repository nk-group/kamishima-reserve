<?php // src/app/Views/Partials/_customer_alert_messages.php ?>

<?php // 1. バリデーションエラー (配列) がある場合の表示 ?>
<?php if (session()->has('errors')) : ?>
    <div class="alert alert-danger alert-dismissible fade show customer-alert" role="alert">
        <div class="alert-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="alert-content">
            <h6 class="alert-heading">入力内容を確認してください</h6>
            <ul class="mb-0 error-list">
                <?php foreach (session('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
    </div>
<?php endif ?>

<?php // 2. 一般的なエラーメッセージ (文字列) がある場合の表示 ?>
<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show customer-alert" role="alert">
        <div class="alert-icon">
            <i class="bi bi-x-circle-fill"></i>
        </div>
        <div class="alert-content">
            <?= esc(session('error')) ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
    </div>
<?php endif ?>

<?php // 3. 成功メッセージがある場合の表示 ?>
<?php if (session()->has('message')): ?>
    <div class="alert alert-success alert-dismissible fade show customer-alert" role="alert">
        <div class="alert-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="alert-content">
            <?= esc(session('message')) ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
    </div>
<?php endif ?>

<?php // 4. 情報メッセージがある場合の表示 ?>
<?php if (session()->has('info')): ?>
    <div class="alert alert-info alert-dismissible fade show customer-alert" role="alert">
        <div class="alert-icon">
            <i class="bi bi-info-circle-fill"></i>
        </div>
        <div class="alert-content">
            <?= esc(session('info')) ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
    </div>
<?php endif ?>

<?php // 5. 警告メッセージがある場合の表示 ?>
<?php if (session()->has('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show customer-alert" role="alert">
        <div class="alert-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="alert-content">
            <?= esc(session('warning')) ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
    </div>
<?php endif ?>

<?php // 6. Clear車検予約専用の特別メッセージ ?>
<?php if (session()->has('reservation_success')): ?>
    <div class="alert alert-success alert-dismissible fade show customer-alert reservation-success-alert" role="alert">
        <div class="alert-icon">
            <i class="bi bi-calendar-check-fill"></i>
        </div>
        <div class="alert-content">
            <h6 class="alert-heading">予約が完了しました</h6>
            <p class="mb-1"><?= esc(session('reservation_success')) ?></p>
            <?php if (session()->has('reservation_number')): ?>
                <div class="reservation-number">
                    <strong>予約番号：<?= esc(session('reservation_number')) ?></strong>
                </div>
            <?php endif ?>
            <?php if (session()->has('reservation_guid_url')): ?>
                <div class="reservation-link">
                    <small>
                        <a href="<?= esc(session('reservation_guid_url')) ?>" target="_blank" class="alert-link">
                            <i class="bi bi-eye"></i> 予約状況を確認する
                        </a>
                    </small>
                </div>
            <?php endif ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
    </div>
<?php endif ?>

<?php // 7. 予約状況確認用メッセージ ?>
<?php if (session()->has('reservation_status_info')): ?>
    <div class="alert alert-info alert-dismissible fade show customer-alert reservation-status-alert" role="alert">
        <div class="alert-icon">
            <i class="bi bi-info-circle-fill"></i>
        </div>
        <div class="alert-content">
            <?= esc(session('reservation_status_info')) ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
    </div>
<?php endif ?>