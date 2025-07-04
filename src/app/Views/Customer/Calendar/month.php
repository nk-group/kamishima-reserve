<?= $this->extend('Layouts/customer_layout') ?>

<?= $this->section('title') ?>
    Clear車検予約カレンダー | 上嶋自動車
<?= $this->endSection() ?>

<?= $this->section('page_header_content') ?>
    <?php if (!($is_iframe ?? false)): ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-calendar3"></i>
            Clear車検予約カレンダー
        </h1>
    </div>
    <?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="calendar-month-page">
        <div class="calendar-container">
            <div class="calendar-header">
                <button class="btn-calendar-action" id="prev-month-btn" 
                        data-month="<?= esc($prev_month) ?>" data-shop-id="<?= esc($shop_id) ?>">
                    <i class="bi bi-chevron-left"></i> 前月
                </button>
                <h2 class="calendar-title" id="calendar-title">
                    <?= esc($current_month_display ?? date('Y年n月')) ?>
                </h2>
                <button class="btn-calendar-action" id="next-month-btn" 
                        data-month="<?= esc($next_month) ?>" data-shop-id="<?= esc($shop_id) ?>">
                    次月 <i class="bi bi-chevron-right"></i>
                </button>
            </div>

            <div class="calendar-month">
                <table class="calendar-table">
                    <thead>
                        <tr class="calendar-header-row">
                            <th class="calendar-header-cell text-center sunday">日</th>
                            <th class="calendar-header-cell text-center">月</th>
                            <th class="calendar-header-cell text-center">火</th>
                            <th class="calendar-header-cell text-center">水</th>
                            <th class="calendar-header-cell text-center">木</th>
                            <th class="calendar-header-cell text-center">金</th>
                            <th class="calendar-header-cell text-center saturday">土</th>
                        </tr>
                    </thead>
                    <tbody id="calendar-body">
                        <?php if (!empty($calendar_data) && is_array($calendar_data)): ?>
                            <?php foreach ($calendar_data as $week): ?>
                                <tr class="calendar-row">
                                    <?php foreach ($week as $day): ?>
                                        <td class="calendar-cell <?= implode(' ', $day['css_classes'] ?? []) ?>" 
                                            data-date="<?= esc($day['date'] ?? '') ?>">
                                            <div class="calendar-date">
                                                <span class="date-number <?= ($day['day_of_week'] ?? 0) == 0 ? 'sunday' : (($day['day_of_week'] ?? 0) == 6 ? 'saturday' : '') ?> <?= !($day['is_current_month'] ?? true) ? 'other-month' : '' ?>">
                                                    <?= esc($day['day'] ?? '') ?>
                                                </span>
                                            </div>
                                            <?php if ($day['is_current_month'] ?? true): ?>
                                                <div class="availability-mark <?= esc($day['availability_status'] ?? 'closed') ?>">
                                                    <?php
                                                    switch ($day['availability_status'] ?? 'closed') {
                                                        case 'available':
                                                            echo '○';
                                                            break;
                                                        case 'limited':
                                                            echo '△';
                                                            break;
                                                        case 'full':
                                                            echo '×';
                                                            break;
                                                        default:
                                                            echo '';
                                                    }
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    カレンダーデータを取得中...
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="calendar-legend">
            <div class="legend-title">予約状況の見方</div>
            <div class="legend-items">
                <span class="legend-item">
                    <span class="availability-mark available">○</span> 余裕あり
                </span>
                <span class="legend-item">
                    <span class="availability-mark limited">△</span> 残りわずか
                </span>
                <span class="legend-item">
                    <span class="availability-mark full">×</span> 空きなし
                </span>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>