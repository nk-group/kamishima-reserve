<?php
/**
 * カレンダーテーブルパーシャル
 * ファイル: src/app/Views/Admin/Dashboard/_calendar_table.php
 * 
 * @var array $calendar_view_data カレンダー表示用データ
 * @var int|null $selected_shop_id 選択された店舗ID
 */
?>

<?php if (!empty($calendar_view_data)): ?>
    <table class="calendar-table">
        <thead>
            <tr class="calendar-header-row">
                <th class="calendar-header-cell sunday">日</th>
                <th class="calendar-header-cell">月</th>
                <th class="calendar-header-cell">火</th>
                <th class="calendar-header-cell">水</th>
                <th class="calendar-header-cell">木</th>
                <th class="calendar-header-cell">金</th>
                <th class="calendar-header-cell saturday">土</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($calendar_view_data as $week): ?>
                <tr>
                    <?php foreach ($week as $day): ?>
                        <td class="calendar-cell<?= $day['is_today'] ? ' today' : '' ?><?= $day['is_holiday'] ? ' holiday' : '' ?>">
                            <div class="calendar-date">
                                <?php if ($day['is_current_month']): ?>
                                    <button class="btn-create-new btn-mini" 
                                            onclick="createNewReservation('<?= esc($day['date']) ?>', <?= $selected_shop_id ?? 'null' ?>)">
                                        ＋新規
                                    </button>
                                <?php endif; ?>
                                <span class="date-number<?= !$day['is_current_month'] ? ' prev-month' : '' ?><?= $day['is_weekend'] ? ($day['date'] && date('w', strtotime($day['date'])) == 0 ? ' sunday' : ' saturday') : '' ?>">
                                    <?= $day['is_current_month'] ? $day['day_num'] : '' ?>
                                </span>
                            </div>
                            
                            <div class="calendar-appointments">
                                <?php if ($day['is_holiday']): ?>
                                    <span class="appointment-new"><?= esc($day['holiday_name']) ?></span>
                                <?php elseif (!empty($day['reservations'])): ?>
                                    <?php 
                                    // 表示する予約数を制限（最大5件）
                                    $displayReservations = array_slice($day['reservations'], 0, 5);
                                    $hiddenCount = count($day['reservations']) - count($displayReservations);
                                    ?>
                                    <?php foreach ($displayReservations as $reservation): ?>
                                        <a href="<?= site_url('admin/reservations/edit/' . $reservation['reservation_id']) ?>" 
                                           class="appointment-time appointment-booked"
                                           title="<?= esc($reservation['customer_name']) ?>様 - <?= esc($reservation['work_type_name']) ?>">
                                            <?= $reservation['reservation_start_time'] ? date('H:i', strtotime($reservation['reservation_start_time'])) : '--:--' ?>
                                            <?= esc(mb_strimwidth($reservation['customer_name'], 0, 10, '...', 'UTF-8')) ?>
                                        </a>
                                    <?php endforeach; ?>
                                    <?php if ($hiddenCount > 0): ?>
                                        <span class="appointment-more">他<?= $hiddenCount ?>件</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($day['is_current_month'] && !$day['is_holiday']): ?>
                                <div class="holiday-label">
                                    <?php
                                    $labels = [];
                                    if (!empty($day['work_type_counts']['clear_shaken'])) {
                                        $labels[] = '車検 ' . $day['work_type_counts']['clear_shaken'] . '件';
                                    }
                                    if (!empty($day['work_type_counts']['general_maintenance'])) {
                                        $labels[] = '整備 ' . $day['work_type_counts']['general_maintenance'] . '件';
                                    }
                                    if (!empty($day['work_type_counts']['other'])) {
                                        $labels[] = 'その他 ' . $day['work_type_counts']['other'] . '件';
                                    }
                                    if (empty($labels) && $day['reservation_count'] > 0) {
                                        $labels[] = '予約 ' . $day['reservation_count'] . '件';
                                    }
                                    if (empty($labels)) {
                                        $labels[] = '予約 0件';
                                    }
                                    echo esc(implode('／', $labels));
                                    ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info" role="alert">
        <i class="bi bi-calendar-x me-2"></i>
        カレンダーデータがありません。
    </div>
<?php endif; ?>