<?= $this->extend('Layouts/customer_layout') ?>

<?= $this->section('title') ?>
    予約状況確認 | 上嶋自動車
<?= $this->endSection() ?>

<?= $this->section('page_header_content') ?>
    <?php if (!($is_iframe ?? false)): ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-calendar-check"></i>
            予約状況確認
        </h1>
    </div>
    <?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="reservation-status-page">
        <?php if (!empty($reservation) && is_object($reservation)): ?>
            <!-- 予約情報表示 -->
            <div class="form-section">
                <div class="section-header">
                    <h3>
                        <i class="bi bi-info-circle"></i>
                        予約情報
                    </h3>
                </div>
                <div class="section-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">予約番号</label>
                            <div class="form-display-value">
                                <?= esc($reservation->reservation_no ?? '') ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">予約状況</label>
                            <div class="form-display-value">
                                <span class="status-badge status-<?= esc($reservation->status_class ?? 'default') ?>">
                                    <?= esc($reservation->status_name ?? '不明') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">作業種別</label>
                            <div class="form-display-value">
                                <?= esc($reservation->work_type_name ?? '') ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">作業店舗</label>
                            <div class="form-display-value">
                                <?= esc($reservation->shop_name ?? '') ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">予約日時</label>
                            <div class="form-display-value">
                                <?= esc($reservation->desired_date_display ?? '') ?>
                                <?php if (!empty($reservation->time_slot_display)): ?>
                                    <br><strong><?= esc($reservation->time_slot_display) ?></strong>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- お客様情報 -->
            <div class="form-section">
                <div class="section-header">
                    <h3>
                        <i class="bi bi-person"></i>
                        お客様情報
                    </h3>
                </div>
                <div class="section-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">お名前</label>
                            <div class="form-display-value">
                                <?= esc($reservation->customer_name ?? '') ?>
                                <?php if (!empty($reservation->customer_kana)): ?>
                                    <br><small>（<?= esc($reservation->customer_kana) ?>）</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">電話番号</label>
                            <div class="form-display-value">
                                <?= esc($reservation->phone_number1 ?? '') ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">メールアドレス</label>
                            <div class="form-display-value">
                                <?= esc($reservation->email ?? '') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 車両情報 -->
            <div class="form-section">
                <div class="section-header">
                    <h3>
                        <i class="bi bi-car-front"></i>
                        車両情報
                    </h3>
                </div>
                <div class="section-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">車両ナンバー</label>
                            <div class="form-display-value">
                                <?= esc($reservation->vehicle_license_region ?? '') ?>
                                <?= esc($reservation->vehicle_license_class ?? '') ?>
                                <?= esc($reservation->vehicle_license_kana ?? '') ?>
                                <?= esc($reservation->vehicle_license_number ?? '') ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">車種</label>
                            <div class="form-display-value">
                                <?= esc($reservation->vehicle_model_name ?? '') ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($reservation->shaken_expiration_date)): ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">車検満了日</label>
                            <div class="form-display-value">
                                <?= esc($reservation->shaken_expiration_date_display ?? '') ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($reservation->notes)): ?>
            <!-- ご要望等 -->
            <div class="form-section">
                <div class="section-header">
                    <h3>
                        <i class="bi bi-chat-text"></i>
                        ご要望・備考
                    </h3>
                </div>
                <div class="section-body">
                    <div class="form-row">
                        <div class="form-group">
                            <div class="form-display-value">
                                <?= nl2br(esc($reservation->notes)) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- アクション -->
            <div class="form-actions-section">
                <div class="action-buttons">
                    <?php if (($reservation->can_cancel ?? false) && ($reservation->status_class ?? '') !== 'cancelled'): ?>
                        <button type="button" class="btn-danger" id="cancel-reservation-btn">
                            <i class="bi bi-x-circle"></i>
                            予約をキャンセルする
                        </button>
                    <?php endif; ?>
                    <a href="tel:<?= esc($shop_phone ?? '0155-24-2510') ?>" class="btn-secondary">
                        <i class="bi bi-telephone"></i>
                        店舗に電話する
                    </a>
                </div>
                <div class="action-note">
                    <small>
                        <i class="bi bi-info-circle"></i>
                        予約内容の変更をご希望の場合は、お電話にてお問い合わせください。
                    </small>
                </div>
            </div>

        <?php else: ?>
            <!-- 予約が見つからない場合 -->
            <div class="form-section">
                <div class="section-body text-center">
                    <div class="error-icon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h3 class="error-title">予約情報が見つかりません</h3>
                    <p class="error-message">
                        指定された予約確認URLは無効または期限切れです。<br>
                        お心当たりがない場合は、店舗までお問い合わせください。
                    </p>
                    <div class="action-buttons">
                        <a href="tel:<?= esc($shop_phone ?? '0155-24-2510') ?>" class="btn-primary">
                            <i class="bi bi-telephone"></i>
                            店舗に電話する
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php // JavaScript用データを非表示で設定 ?>
    <div id="reservation-status-data" style="display: none;"
         data-reservation-guid="<?= esc($reservation->reservation_guid ?? '') ?>"
         data-base-url="/customer/reservation/status">
    </div>
<?= $this->endSection() ?>