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
                <button class="btn-calendar-action" id="prev-week-btn" 
                        data-week="<?= esc($prev_week) ?>" data-shop-id="<?= esc($shop_id) ?>">
                    <i class="bi bi-chevron-left"></i> 前週
                </button>
                <h2 class="calendar-title" id="calendar-title">
                    <?= esc($current_week_display ?? date('Y年n月j日') . ' - ' . date('Y年n月j日', strtotime('+6 days'))) ?>
                </h2>
                <button class="btn-calendar-action" id="next-week-btn" 
                        data-week="<?= esc($next_week) ?>" data-shop-id="<?= esc($shop_id) ?>">
                    次週 <i class="bi bi-chevron-right"></i>
                </button>
            </div>

            <div class="calendar-week">
                <div class="week-calendar-scroll">
                    <table class="calendar-table">
                        <thead>
                            <tr class="calendar-header-row">
                                <th class="calendar-header-cell text-center">時間</th>
                                <?php if (!empty($week_dates) && is_array($week_dates)): ?>
                                    <?php foreach ($week_dates as $date): ?>
                                        <th class="calendar-header-cell text-center <?= ($date['day_of_week'] ?? 0) == 0 ? 'sunday' : (($date['day_of_week'] ?? 0) == 6 ? 'saturday' : '') ?> <?= ($date['is_today'] ?? false) ? 'today' : '' ?>">
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
                                    <th class="calendar-header-cell text-center sunday">日</th>
                                    <th class="calendar-header-cell text-center">月</th>
                                    <th class="calendar-header-cell text-center">火</th>
                                    <th class="calendar-header-cell text-center">水</th>
                                    <th class="calendar-header-cell text-center">木</th>
                                    <th class="calendar-header-cell text-center">金</th>
                                    <th class="calendar-header-cell text-center saturday">土</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="week-calendar-body">
                            <?php if (!empty($time_slots) && is_array($time_slots)): ?>
                                <?php foreach ($time_slots as $timeSlot): ?>
                                    <tr class="time-slot-row">
                                        <td class="time-label-cell text-center">
                                            <?= esc($timeSlot['name'] ?? '') ?>
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
        <div class="calendar-bottom-controls">
            <button class="btn-calendar-nav" id="back-to-month-btn" 
                    data-shop-id="<?= esc($shop_id) ?>">
                <i class="bi bi-calendar3"></i> 月表示に戻る
            </button>
        </div>
    </div>
<?= $this->endSection() ?>