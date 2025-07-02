<?= $this->extend('Layouts/customer_layout') ?>

<?= $this->section('title') ?>
    Clear車検予約 週表示 | 上嶋自動車
<?= $this->endSection() ?>

<?= $this->section('page_header_content') ?>
    <?php if (!($is_iframe ?? false)): ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-calendar-week"></i>
            Clear車検予約 週表示
        </h1>
    </div>
    <?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="calendar-week-page">
        <div class="calendar-container">
            <div class="calendar-header">
                <button class="btn-calendar-nav" id="prev-week-btn">
                    <i class="bi bi-chevron-left"></i> 前週
                </button>
                <h2 class="calendar-title" id="calendar-title">
                    <?= esc($current_week_display ?? date('Y年n月j日') . ' - ' . date('Y年n月j日', strtotime('+6 days'))) ?>
                </h2>
                <button class="btn-calendar-nav" id="next-week-btn">
                    次週 <i class="bi bi-chevron-right"></i>
                </button>
            </div>

            <div class="calendar-week">
                <div class="week-calendar-scroll">
                    <table class="week-calendar-table">
                        <thead>
                            <tr class="week-header-row">
                                <th class="week-header-cell">時間</th>
                                <?php if (!empty($week_dates) && is_array($week_dates)): ?>
                                    <?php foreach ($week_dates as $date): ?>
                                        <th class="week-header-cell <?= ($date['day_of_week'] ?? 0) == 0 ? 'sunday' : (($date['day_of_week'] ?? 0) == 6 ? 'saturday' : '') ?> <?= ($date['is_today'] ?? false) ? 'today' : '' ?>">
                                            <div class="date-info">
                                                <div class="date-number">
                                                    <?= esc($date['day'] ?? '') ?>
                                                </div>
                                                <div class="date-label">
                                                    <?= esc($date['day_label'] ?? '') ?>
                                                </div>
                                            </div>
                                        </th>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <th class="week-header-cell">日</th>
                                    <th class="week-header-cell">月</th>
                                    <th class="week-header-cell">火</th>
                                    <th class="week-header-cell">水</th>
                                    <th class="week-header-cell">木</th>
                                    <th class="week-header-cell">金</th>
                                    <th class="week-header-cell">土</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="week-calendar-body">
                            <?php if (!empty($time_slots) && is_array($time_slots)): ?>
                                <?php foreach ($time_slots as $timeSlot): ?>
                                    <tr class="time-slot-row">
                                        <td class="time-slot-label-cell">
                                            <div class="time-range">
                                                <?= esc($timeSlot['start_time_display'] ?? '') ?>
                                            </div>
                                            <div class="time-duration">
                                                <?= esc($timeSlot['duration_display'] ?? '') ?>
                                            </div>
                                        </td>
                                        <?php if (!empty($week_dates) && is_array($week_dates)): ?>
                                            <?php foreach ($week_dates as $date): ?>
                                                <td class="time-slot-cell <?= implode(' ', $timeSlot['slots'][$date['date']]['css_classes'] ?? []) ?> <?= ($date['is_today'] ?? false) ? 'today-column' : '' ?>" 
                                                    data-date="<?= esc($date['date'] ?? '') ?>">
                                                    <?php 
                                                    $slotData = $timeSlot['slots'][$date['date']] ?? [];
                                                    $status = $slotData['status'] ?? 'closed';
                                                    ?>
                                                    <?php if ($status === 'available' || $status === 'limited'): ?>
                                                        <button class="time-slot-button" 
                                                                data-time-slot-id="<?= esc($slotData['time_slot_id'] ?? '') ?>">
                                                            <?= $status === 'available' ? '予約可' : '残りわずか' ?>
                                                        </button>
                                                    <?php elseif ($status === 'full'): ?>
                                                        <div class="unavailable-mark">×</div>
                                                        <div class="unavailable-text">満席</div>
                                                    <?php else: ?>
                                                        <div class="closed-mark">-</div>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <?php for ($i = 0; $i < 7; $i++): ?>
                                                <td class="time-slot-cell closed">
                                                    <div class="closed-mark">-</div>
                                                </td>
                                            <?php endfor; ?>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">
                                        時間帯データを取得中...
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="calendar-legend">
            <div class="legend-title">予約状況の見方</div>
            <div class="legend-items">
                <span class="legend-item">
                    <button class="time-slot-button available" disabled>予約可</button> 余裕あり
                </span>
                <span class="legend-item">
                    <button class="time-slot-button limited" disabled>残りわずか</button> 残りわずか
                </span>
                <span class="legend-item">
                    <span class="unavailable-mark">×</span> 満席
                </span>
                <span class="legend-item">
                    <span class="closed-mark">-</span> 定休日
                </span>
            </div>
        </div>
    </div>

    <?php // JavaScript用データを非表示で設定 ?>
    <div id="calendar-week-data" style="display: none;"
         data-current-week-start="<?= esc($current_week_start ?? date('Y-m-d', strtotime('monday this week'))) ?>"
         data-base-url="/customer/calendar/week">
    </div>
<?= $this->endSection() ?>