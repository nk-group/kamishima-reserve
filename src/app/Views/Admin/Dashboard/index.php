<?= $this->extend('Layouts/admin_layout') ?>

<?= $this->section('title') ?>
    <?= esc($page_title ?? 'ダッシュボード') ?>
<?= $this->endSection() ?>

<?= $this->section('page_header_content') ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-house-fill"></i>
            <?= esc($h1_title) ?>
        </h1>
        <div class="header-info">
            <?= date('Y年n月j日 (D)') ?>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="page-content">
        <?php // エラーメッセージ表示 ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= esc($error_message) ?>
            </div>
        <?php endif; ?>

        <!-- Today's Schedule -->
        <div class="today-schedule">
            <div class="section-header">
                <div class="d-flex align-items-center">
                    <h2 class="section-title">
                        <i class="bi bi-calendar-day"></i>
                        本日の予定
                    </h2>
                    <span class="section-date"><?= esc($today_date ?? date('Y年n月j日')) ?></span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn-outline-custom btn-small" id="refresh-today-btn">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        更新
                    </button>
                    <a href="#" class="btn-entry">
                        <i class="bi bi-printer me-2"></i>
                        入庫予定表印刷
                    </a>
                </div>
            </div>
            
            <div class="schedule-grid" id="schedule-grid">
                <?php if (!empty($today_reservations)): ?>
                    <?php foreach ($today_reservations as $index => $reservation): ?>
                        <div class="schedule-card<?= $index >= 6 ? ' schedule-card-hidden' : '' ?>" 
                             style="<?= $index >= 6 ? 'display: none;' : '' ?>">
                            <div class="schedule-time">
                                <i class="bi bi-clock"></i>
                                <?= $reservation['reservation_start_time'] ? date('H:i', strtotime($reservation['reservation_start_time'])) : '--:--' ?>
                            </div>
                            <div class="customer-name">
                                <i class="bi bi-person-circle"></i>
                                <?= esc($reservation['customer_name']) ?>様
                            </div>
                            <div class="work-type-badge badge-<?= esc($reservation['work_type_code'] ?? 'other') ?>">
                                <?= esc($reservation['work_type_name'] ?? '') ?>
                                <?php if (!empty($reservation['shop_name'])): ?>
                                    （<?= esc($reservation['shop_name']) ?>）
                                <?php endif; ?>
                            </div>
                            <div class="schedule-details">
                                <div><strong>連絡先：</strong><?= esc($reservation['phone_number1'] ?? '') ?></div>
                                <div><strong>車種／車番：</strong>
                                    <?= esc($reservation['vehicle_model_name'] ?? '') ?>
                                    <?= esc($reservation['vehicle_license_region'] ?? '') ?>
                                    <?= esc($reservation['vehicle_license_class'] ?? '') ?>
                                    <?= esc($reservation['vehicle_license_kana'] ?? '') ?>
                                    <?= esc($reservation['vehicle_license_number'] ?? '') ?>
                                </div>
                                <?php if (!empty($reservation['shaken_expiration_date'])): ?>
                                    <div><strong>車検満了：</strong><?= date('Y年n月j日', strtotime($reservation['shaken_expiration_date'])) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        本日の予約はありません。
                    </div>
                <?php endif; ?>
            </div>
    
            <?php if (!empty($today_reservations) && count($today_reservations) > 6): ?>
                <div class="more-link">
                    <a href="#" id="toggle-more-reservations" data-show-text="もっと見る" data-hide-text="表示を少なくする">
                        <i class="bi bi-arrow-right me-2"></i>
                        もっと見る（残り<?= count($today_reservations) - 6 ?>件）
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Calendar Section -->
        <div class="calendar-section">
            <div class="calendar-header">
                <div>
                    <h2 class="section-title">
                        <i class="bi bi-calendar3"></i>
                        予約カレンダー
                    </h2>
                    <div class="calendar-stats" id="calendar-stats">
                        合計件数　<?= esc($statistics['total'] ?? 0) ?>件／
                        Clear車検　<?= esc($statistics['clear_shaken'] ?? 0) ?>件／
                        一般整備　<?= esc($statistics['general_maintenance'] ?? 0) ?>件／
                        その他　<?= esc($statistics['other'] ?? 0) ?>件
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn-outline-custom btn-small" id="refresh-calendar-btn">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        更新
                    </button>
                    <label for="shopSelect" class="form-label me-2" style="color: #1f2937; font-weight: 600;">作業店舗</label>
                    <select id="shopSelect" class="shop-select">
                        <option value="">全店舗</option>
                        <?php if (!empty($shops)): ?>
                            <?php foreach ($shops as $shop): ?>
                                <option value="<?= esc($shop->id) ?>" 
                                    <?= $selected_shop_id == $shop->id ? 'selected' : '' ?>>
                                    <?= esc($shop->name) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="calendar-container">
                <div class="calendar-nav">
                    <button class="btn-primary-custom btn-small" id="prev-month-btn">
                        <i class="bi bi-chevron-left me-1"></i>
                        前月
                    </button>
                    <div class="calendar-month" id="calendar-month">
                        <?= esc($current_month_display ?? date('Y年n月')) ?>
                    </div>
                    <button class="btn-primary-custom btn-small" id="next-month-btn">
                        次月
                        <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </div>

                <div class="calendar-scroll-container">
                    <div class="calendar-table-wrapper" id="calendar-container">
                        <?= $this->include('Admin/Dashboard/_calendar_table', [
                            'calendar_view_data' => $calendar_view_data ?? [],
                            'selected_shop_id' => $selected_shop_id
                        ]) ?>
                    </div>
                </div>

                <div class="calendar-pagination">
                    <div class="pagination-dot active"></div>
                    <div class="pagination-dot"></div>
                    <div class="pagination-dot"></div>
                    <div class="pagination-dot"></div>
                    <div class="pagination-dot"></div>
                </div>
            </div>
        </div>
    </div>

    <?php // 隠し要素：JavaScript用データ ?>
    <div id="dashboard-data" style="display: none;" 
         data-current-month="<?= esc($current_month ?? date('Y-m')) ?>"
         data-selected-shop-id="<?= esc($selected_shop_id ?? '') ?>"
         data-prev-month="<?= esc($prev_month ?? '') ?>"
         data-next-month="<?= esc($next_month ?? '') ?>">
    </div>
<?= $this->endSection() ?>