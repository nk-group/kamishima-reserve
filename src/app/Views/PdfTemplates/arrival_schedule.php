<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            padding-top: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }
        
        .title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .schedule-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 10px 5px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            height: 35px;
        }
        
        .schedule-table td {
            border: 1px solid #000;
            padding: 10px 5px;
            text-align: center;
            vertical-align: middle;
            height: 75px;
            font-size: 13px;
            line-height: 1.4;
        }
        
        /* 時間列の背景色削除 */
        td[style*="background-color: #f8f8f8"] {
            font-weight: bold;
            font-size: 14px;
        }
        
        /* 氏名列のフォントサイズ */
        .name-column {
            font-size: 15px;
            font-weight: bold;
        }
        
        .remarks-column {
            font-size: 12px;
        }
        
        /* 午後行専用スタイル */
        .section-header-cell {
            background-color: #d0d0d0;
            font-weight: bold;
            text-align: center;
            height: 15px;
            font-size: 14px;
            padding: 2px 5px;
        }
        
        .total-row {
            background-color: #e0e0e0;
            font-weight: bold;
            height: 45px;
            font-size: 14px;
        }
        
        .footer-note {
            margin-top: 15px;
            font-size: 11px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title"><?= esc($formatted_date) ?> クリア入庫予定表</div>
    </div>

    <table class="schedule-table">
        <thead>
            <tr>
                <th style="width: 50px;">入庫時間</th>
                <th style="width: 120px;">氏名/ナンバー</th>
                <th style="width: 65px;">年式</th>
                <th style="width: 100px;">車種</th>
                <th style="width: 120px;">備考</th>
                <th style="width: 70px;">代車</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $morningSlots = ['8:45', '9:30', '10:15', '11:00'];
            $afternoonSlots = ['13:00', '13:45', '14:30', '15:15', '16:00', '16:45'];
            
            // 予約データを時間でグループ化
            $reservationsByTime = [];
            foreach ($reservations as $reservation) {
                $reservationsByTime[$reservation['reservation_time']] = $reservation;
            }
            ?>
            
            <!-- 午前スロット -->
            <?php foreach ($morningSlots as $timeSlot): ?>
                <?php $reservation = $reservationsByTime[$timeSlot] ?? null; ?>
                <?php 
                // 11:00は行全体をグレー背景
                $bgColor = ($timeSlot === '11:00') ? '#f0f0f0' : '';
                $bgStyle = $bgColor ? "background-color: {$bgColor};" : '';
                ?>
                <tr>
                    <td style="font-weight: bold; font-size: 14px; <?= $bgStyle ?>"><?= esc($timeSlot) ?></td>
                    <?php if ($reservation): ?>
                        <td class="name-column" style="<?= $bgStyle ?>">
                            <?= esc($reservation['customer_name']) ?><br>
                            <?= esc($reservation['vehicle_number'] ?? '帯広330あ1234') ?>
                        </td>
                        <td style="<?= $bgStyle ?>"><?= esc($reservation['year_month']) ?></td>
                        <td style="<?= $bgStyle ?>"><?= esc($reservation['vehicle_info']) ?></td>
                        <td class="remarks-column" style="<?= $bgStyle ?>"><?= esc($reservation['remarks']) ?></td>
                        <td style="<?= $bgStyle ?>"><?= esc($reservation['substitute_car'] ?? '') ?></td>
                    <?php else: ?>
                        <td class="name-column" style="<?= $bgStyle ?>">
                            <br>
                        </td>
                        <td style="<?= $bgStyle ?>"></td>
                        <td style="<?= $bgStyle ?>"></td>
                        <td style="<?= $bgStyle ?>"></td>
                        <td style="<?= $bgStyle ?>"></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            
            <!-- 午後ヘッダー -->
            <tr style="height: 15px;">
                <td colspan="6" style="background-color: #d0d0d0; font-weight: bold; text-align: center; font-size: 14px; padding: 2px 5px; height: 15px;">午　後</td>
            </tr>
            
            <!-- 午後スロット -->
            <?php foreach ($afternoonSlots as $timeSlot): ?>
                <?php $reservation = $reservationsByTime[$timeSlot] ?? null; ?>
                <?php 
                // 13:00と16:45は行全体をグレー背景
                $bgColor = (in_array($timeSlot, ['13:00', '16:45'])) ? '#f0f0f0' : '';
                $bgStyle = $bgColor ? "background-color: {$bgColor};" : '';
                ?>
                <tr>
                    <td style="font-weight: bold; font-size: 14px; <?= $bgStyle ?>"><?= esc($timeSlot) ?></td>
                    <?php if ($reservation): ?>
                        <td class="name-column" style="<?= $bgStyle ?>">
                            <?= esc($reservation['customer_name']) ?><br>
                            <?= esc($reservation['vehicle_number'] ?? '帯広330あ5678') ?>
                        </td>
                        <td style="<?= $bgStyle ?>"><?= esc($reservation['year_month']) ?></td>
                        <td style="<?= $bgStyle ?>"><?= esc($reservation['vehicle_info']) ?></td>
                        <td class="remarks-column" style="<?= $bgStyle ?>"><?= esc($reservation['remarks']) ?></td>
                        <td style="<?= $bgStyle ?>"><?= esc($reservation['substitute_car'] ?? '') ?></td>
                    <?php else: ?>
                        <td class="name-column" style="<?= $bgStyle ?>">
                            <br>
                        </td>
                        <td style="<?= $bgStyle ?>"></td>
                        <td style="<?= $bgStyle ?>"></td>
                        <td style="<?= $bgStyle ?>"></td>
                        <td style="<?= $bgStyle ?>"></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- 合計行 -->
    <table class="schedule-table">
        <tr class="total-row">
            <td colspan="5" style="text-align: center; padding: 10px; background-color: #e0e0e0;">
                <strong>合計</strong>
            </td>
            <td style="text-align: center; padding: 10px; background-color: #e0e0e0;">
                <strong><?= count($reservations) ?>台</strong>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        ※平日の最終入庫は、16:00とする。日曜等、入庫が多い場合は、16:45を設ける。
    </div>
</body>
</html>