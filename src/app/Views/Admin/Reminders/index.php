<?= $this->extend('Layouts/admin_layout') ?>

<?= $this->section('title') ?>
    リマインドメール送信予定一覧
<?= $this->endSection() ?>

<?= $this->section('page_header_content') ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-envelope-check"></i>
            リマインドメール送信予定一覧
        </h1>
    </div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="page-content">
        <?= $this->include('Partials/_alert_messages') ?>

        <!-- Search Section -->
        <div class="search-form mb-4">
            <h5><i class="bi bi-funnel"></i> 検索条件</h5>
            <?= form_open(route_to('admin.reminders.index'), ['method' => 'get']) ?>
                <div class="row">
                    <div class="col-md-3">
                        <label for="send_status" class="form-label">送信ステータス</label>
                        <?= form_dropdown('send_status', $send_status_options, $filters['send_status'] ?? '', ['class' => 'form-select', 'id' => 'send_status']) ?>
                    </div>
                    <div class="col-md-3">
                        <label for="customer_name" class="form-label">お客様氏名</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control" value="<?= esc($filters['customer_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="vehicle_number" class="form-label">車両ナンバー</label>
                        <input type="text" name="vehicle_number" id="vehicle_number" class="form-control" value="<?= esc($filters['vehicle_number'] ?? '') ?>" placeholder="番号部分で検索">
                    </div>
                    <div class="col-md-3">
                        <label for="shop_id" class="form-label">作業店舗</label>
                        <?= form_dropdown('shop_id', ['' => '全店舗'] + array_column($shops, 'name', 'id'), $filters['shop_id'] ?? '', ['class' => 'form-select', 'id' => 'shop_id']) ?>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="work_type_id" class="form-label">次回作業種別</label>
                        <?= form_dropdown('work_type_id', ['' => 'すべて'] + array_column($work_types, 'name', 'id'), $filters['work_type_id'] ?? '', ['class' => 'form-select', 'id' => 'work_type_id']) ?>
                    </div>
                    <div class="col-md-4">
                        <label for="next_inspection_date_from" class="form-label">次回点検日</label>
                        <div class="input-group">
                            <input type="text" name="next_inspection_date_from" class="form-control flatpickr-date" placeholder="開始日" value="<?= esc($filters['next_inspection_date_from'] ?? '') ?>">
                            <span class="input-group-text">～</span>
                            <input type="text" name="next_inspection_date_to" class="form-control flatpickr-date" placeholder="終了日" value="<?= esc($filters['next_inspection_date_to'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-5 d-flex align-items-end">
                        <button type="submit" class="btn-search me-2">
                            <i class="bi bi-search"></i> 検索
                        </button>
                        <a href="<?= route_to('admin.reminders.index') ?>" class="btn-clear">
                            <i class="bi bi-arrow-clockwise"></i> リセット
                        </a>
                    </div>
                </div>
            <?= form_close() ?>
        </div>

        <!-- Results Section -->
        <div class="results-section">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <?php helper('pagination'); // ソート用ヘルパーを読み込み ?>
                        <tr>
                            <th>送信ステータス</th>
                            <th>
                                <a href="<?= buildSortUrl('sent_at', 'admin.reminders.index') ?>" class="sort-link">
                                    送信予定/完了日時
                                    <?= renderSortIcon('sent_at') ?>
                                </a>
                            </th>
                            <th>
                                <a href="<?= buildSortUrl('customer_name', 'admin.reminders.index') ?>" class="sort-link">
                                    お客様氏名
                                    <?= renderSortIcon('customer_name') ?>
                                </a>
                            </th>
                            <th>メールアドレス</th>
                            <th>
                                <a href="<?= buildSortUrl('next_inspection_date', 'admin.reminders.index') ?>" class="sort-link">
                                    次回点検日
                                    <?= renderSortIcon('next_inspection_date') ?>
                                </a>
                            </th>
                            <th>次回作業種別</th>
                            <th>エラー概要</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reminders)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-envelope-slash fs-1 d-block mb-2"></i>
                                    該当するリマインド予定が見つかりませんでした。
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php
                            // Helper function for status badge
                            if (!function_exists('render_reminder_status_badge')) {
                                function render_reminder_status_badge($status) {
                                    switch ($status) {
                                        case 'success':
                                            return '<span class="status-badge status-success">送信成功</span>';
                                        case 'failed':
                                            return '<span class="status-badge status-failed">送信失敗</span>';
                                        default:
                                            return '<span class="status-badge status-pending">未送信</span>';
                                    }
                                }
                            }
                            ?>
                            <?php foreach ($reminders as $reminder): ?>
                                <tr>
                                    <td>
                                        <?= render_reminder_status_badge($reminder->reminder_status) ?>
                                    </td>
                                    <td>
                                        <?php if ($reminder->sent_at): ?>
                                            <?= esc(date('Y-m-d H:i', strtotime($reminder->sent_at))) ?>
                                        <?php elseif ($reminder->next_inspection_date): ?>
                                            <?php // コントローラのソートロジックに合わせて送信予定日を計算 ?>
                                            <?= esc(date('Y-m-d', strtotime('-30 days', strtotime($reminder->next_inspection_date)))) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($reminder->customer_name) ?></td>
                                    <td><?= esc($reminder->email) ?></td>
                                    <td><?= esc($reminder->next_inspection_date) ?></td>
                                    <td><?= esc($reminder->getNextWorkType()->name ?? '不明') ?></td>
                                    <td class="text-danger" title="<?= esc($reminder->error_message) ?>">
                                        <?= esc(mb_strimwidth($reminder->error_message ?? '', 0, 40, '...')) ?>
                                    </td>
                                    <td>
                                        <a href="<?= route_to('admin.reservations.edit', $reminder->id) ?>" class="btn-action btn-edit btn-small" title="元予約の修正">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                <div class="pagination-container">
                    <?= $pager->links('default', 'default_full') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?= $this->endSection() ?>
