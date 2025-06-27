<?= $this->extend('Layouts/admin_layout') ?>

<?= $this->section('title') ?>
    <?= esc($page_title ?? '予約検索／一覧') ?>
<?= $this->endSection() ?>

<?= $this->section('page_header_content') ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-search"></i>
            <?= esc($h1_title) ?>
        </h1>
    </div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="page-content">
        <?= $this->include('Partials/_alert_messages') ?>

        <!-- Search Section -->
        <?= form_open(route_to('admin.reservations.index'), ['method' => 'get', 'id' => 'search-form']) ?>
        <div class="search-section">
            <h2 class="search-title">
                <i class="bi bi-funnel"></i>
                検索条件
            </h2>

            <div class="search-row search-row-main">
                <div class="search-group narrow">
                    <label class="form-label">お名前</label>
                    <input type="text" name="customer_name" class="form-control" 
                           value="<?= esc($search_params['customer_name'] ?? '') ?>" placeholder="">
                </div>
                <div class="search-group narrow">
                    <label class="form-label">車番</label>
                    <input type="text" name="vehicle_number" class="form-control" 
                           value="<?= esc($search_params['vehicle_number'] ?? '') ?>" placeholder="">
                </div>
                <div class="search-group narrow">
                    <label class="form-label">LINE識別</label>
                    <input type="text" name="line_display_name" class="form-control" 
                           value="<?= esc($search_params['line_display_name'] ?? '') ?>" placeholder="">
                </div>
                <div class="search-group medium">
                    <label class="form-label">作業店舗</label>
                    <select name="shop_id" class="form-select">
                        <option value="">すべて</option>
                        <?php foreach ($shops as $shop): ?>
                            <option value="<?= esc($shop->id) ?>" 
                                <?= set_select('shop_id', $shop->id, ($search_params['shop_id'] ?? '') == $shop->id) ?>>
                                <?= esc($shop->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="search-group wide search-group-date-pc">
                    <label class="form-label">予約希望日</label>
                    <div class="date-range-container">
                        <input type="text" name="date_from" class="form-control flatpickr-date" 
                               value="<?= esc($search_params['date_from'] ?? '') ?>" placeholder="開始日">
                        <span class="date-separator">～</span>
                        <input type="text" name="date_to" class="form-control flatpickr-date" 
                               value="<?= esc($search_params['date_to'] ?? '') ?>" placeholder="終了日">
                    </div>
                </div>
            </div>

            <div class="search-row search-row-date">
                <div class="search-group wide">
                    <label class="form-label">予約希望日</label>
                    <div class="date-range-container">
                        <input type="text" name="date_from" class="form-control flatpickr-date" 
                               value="<?= esc($search_params['date_from'] ?? '') ?>" placeholder="開始日">
                        <span class="date-separator">～</span>
                        <input type="text" name="date_to" class="form-control flatpickr-date" 
                               value="<?= esc($search_params['date_to'] ?? '') ?>" placeholder="終了日">
                    </div>
                </div>
            </div>

            <div class="search-row">
                <div class="search-group work-types-group">
                    <label class="form-label">作業種別</label>
                    <div class="checkbox-group">
                        <?php foreach ($work_types as $workType): ?>
                            <div class="checkbox-item">
                                <input type="checkbox" class="form-check-input" 
                                       id="work_type_<?= $workType->id ?>" 
                                       name="work_type_ids[]" 
                                       value="<?= $workType->id ?>"
                                       <?= in_array($workType->id, $search_params['work_type_ids'] ?? []) ? 'checked' : '' ?>>
                                <label for="work_type_<?= $workType->id ?>" class="form-check-label">
                                    <?= esc($workType->name) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quick Search -->
                <div class="quick-search">
                    <label class="form-label">クイック検索</label>
                    <div class="quick-buttons">
                        <?php foreach ($quick_searches as $key => $label): ?>
                            <button type="button" class="btn btn-quick-small" data-quick="<?= esc($key) ?>">
                                <?= esc($label) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Search Actions -->
                <div class="search-actions-mobile">
                    <button type="submit" class="btn-search">
                        <i class="bi bi-search me-2"></i>検索
                    </button>
                    <button type="button" class="btn-clear" id="clear-search">
                        <i class="bi bi-arrow-clockwise me-2"></i>条件クリア
                    </button>
                </div>
            </div>
        </div>
        <?= form_close() ?>

        <!-- Results Section -->
        <div class="results-section">
            <div class="results-header">
                <div class="results-count">
                    検索結果：<?= number_format($pagination['total']) ?>件
                    <?php if (!empty($statistics['by_status'])): ?>
                        <small class="text-muted ms-3"><?php foreach ($statistics['by_status'] as $stat): ?><?= esc($stat['status_name']) ?>: <?= number_format($stat['count']) ?>件　<?php endforeach; ?></small>
                    <?php endif; ?>
                </div>
                <a href="<?= route_to('admin.reservations.new') ?>" class="btn-create-new">
                    <i class="bi bi-plus-circle me-2"></i>新規予約作成
                </a>
            </div>

            <!-- Results Table -->
            <?php if (!empty($reservations)): ?>
                <div class="table-container">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <a href="<?= buildSortUrl('reservation_no') ?>" class="sort-link">
                                        予約番号
                                        <?= renderSortIcon('reservation_no') ?>
                                    </a>
                                </th>
                                <th>予約状況</th>
                                <th>
                                    <a href="<?= buildSortUrl('desired_date') ?>" class="sort-link">
                                        予約希望日
                                        <?= renderSortIcon('desired_date') ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="<?= buildSortUrl('customer_name') ?>" class="sort-link">
                                        お名前
                                        <?= renderSortIcon('customer_name') ?>
                                    </a>
                                </th>
                                <th>車種</th>
                                <th>車番</th>
                                <th>作業種別</th>
                                <th>作業店舗</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td><?= esc($reservation->reservation_no) ?></td>
                                    <td>
                                        <?php
                                        $status = $reservation->getReservationStatus();
                                        $statusClass = 'status-unknown'; // デフォルトクラス
                                        if ($status) {
                                            // 'pending' -> 'status-pending' のように動的にクラス名を生成
                                            $statusClass = 'status-' . esc($status->getCode(), 'attr');
                                        }
                                        ?>
                                        <span class="status-badge <?= $statusClass ?>">
                                            <?= $status ? esc($status->name) : '不明' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $reservation->desired_date ? date('Y/m/d', strtotime($reservation->desired_date)) : '' ?>
                                        <?php if ($reservation->reservation_start_time): ?>
                                            <?= date('H:i', strtotime($reservation->reservation_start_time)) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($reservation->customer_name) ?></td>
                                    <td><?= esc($reservation->vehicle_model_name) ?></td>
                                    <td><?= esc($reservation->getFullLicensePlate()) ?></td>
                                    <td>
                                        <?php
                                        $workType = $reservation->getWorkType();
                                        $backgroundColor = '#e9ecef'; // デフォルトの背景色
                                        $textColor = '#495057';       // デフォルトのテキスト色
                                        $borderStyle = '';            // デフォルトのボーダースタイル

                                        if ($workType && !empty($workType->tag_color)) {
                                            $bgColor = $workType->tag_color;
                                            $backgroundColor = esc($bgColor, 'attr');

                                            // 16進数カラーコードをRGBに変換
                                            $hex = ltrim($bgColor, '#');
                                            if (strlen($hex) === 3) {
                                                $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
                                                $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
                                                $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
                                            } else {
                                                $r = hexdec(substr($hex, 0, 2));
                                                $g = hexdec(substr($hex, 2, 2));
                                                $b = hexdec(substr($hex, 4, 2));
                                            }

                                            // 輝度を計算 (YIQ式)
                                            $luminance = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

                                            // 輝度が128以上（明るい色）なら暗いテキスト、未満（暗い色）なら明るいテキスト
                                            $textColor = ($luminance >= 128) ? '#343a40' : '#ffffff';

                                            // 背景が白(#ffffff)の場合、薄いグレーの枠線を追加して視認性を確保
                                            if (strtolower($bgColor) === '#ffffff') {
                                                $borderStyle = 'border: 1px solid #dee2e6;';
                                            }
                                        }
                                        ?>
                                        <span class="work-type-tag" style="background-color: <?= $backgroundColor ?>; color: <?= $textColor ?>; <?= $borderStyle ?>">
                                            <?= $workType ? esc($workType->name) : '不明' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $shop = $reservation->getShop();
                                        echo $shop ? esc($shop->name) : '不明';
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?= route_to('admin.reservations.edit', $reservation->id) ?>" class="btn-action btn-edit">
                                            修正
                                        </a>
                                        <?php if (!$reservation->isCompleted()): ?>
                                            <button type="button" class="btn-action btn-complete" 
                                                    onclick="markAsComplete(<?= $reservation->id ?>)">
                                                完了
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="pagination-container">
                        <ul class="pagination">
                            <?php if ($pagination['has_previous']): ?>
                                <li><a href="<?= buildPageUrl($pagination['previous_page']) ?>">前へ</a></li>
                            <?php endif; ?>
                            
                            <?php 
                            $startPage = max(1, $pagination['current_page'] - 2);
                            $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);
                            ?>
                            
                            <?php if ($startPage > 1): ?>
                                <li><a href="<?= buildPageUrl(1) ?>">1</a></li>
                                <?php if ($startPage > 2): ?>
                                    <li><span>...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="<?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                    <a href="<?= buildPageUrl($i) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($endPage < $pagination['total_pages']): ?>
                                <?php if ($endPage < $pagination['total_pages'] - 1): ?>
                                    <li><span>...</span></li>
                                <?php endif; ?>
                                <li><a href="<?= buildPageUrl($pagination['total_pages']) ?>"><?= $pagination['total_pages'] ?></a></li>
                            <?php endif; ?>
                            
                            <?php if ($pagination['has_next']): ?>
                                <li><a href="<?= buildPageUrl($pagination['next_page']) ?>">次へ</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Export Actions -->
                <div class="export-actions">
                    <button type="button" class="btn-export" id="copy-to-clipboard">
                        <i class="bi bi-clipboard me-2"></i>クリップボード
                    </button>
                    <a href="<?= route_to('admin.reservations.export-csv') . '?' . http_build_query($search_params) ?>" 
                       class="btn-export">
                        <i class="bi bi-download me-2"></i>CSVダウンロード
                    </a>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    検索条件に該当する予約が見つかりませんでした。
                </div>
            <?php endif; ?>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('page_specific_scripts') ?>
<script>
// PHP側のデータをJavaScript側に渡す
window.reservationListData = {
    searchParams: <?= json_encode($search_params) ?>,
    pagination: <?= json_encode($pagination) ?>,
    quickSearches: <?= json_encode($quick_searches) ?>
};

console.log('Reservation list data loaded:', window.reservationListData);
</script>
<?= $this->endSection() ?>