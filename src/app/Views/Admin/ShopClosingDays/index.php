<?= $this->section('content') ?>
    <div class="page-content shop-closing-days-index"><?antml:parameter>
<parameter name="old_str"><?= $this->section('content') ?>
    <div class="page-content"><?= $this->extend('Layouts/admin_layout') ?>

<?= $this->section('title') ?>
    <?= esc($page_title) ?>
<?= $this->endSection() ?>

<?= $this->section('page_specific_head') ?>
    <?php 
    // Body IDを設定（admin.jsで動的インポートに使用）
    $body_id = 'page-admin-shop-closing-days-index';
    ?>
    <style>
        .closing-day-badge {
            font-size: 0.75rem;
            margin-right: 0.25rem;
        }
        .search-form {
            background: #f8f9fa;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .table-actions {
            white-space: nowrap;
        }
        .table-actions .btn {
            margin-right: 0.25rem;
        }
        .filter-count {
            color: #6c757d;
            font-size: 0.875rem;
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('page_header_content') ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-calendar-x"></i>
            <?= esc($h1_title) ?>
        </h1>
        <div class="header-actions">
            <a href="<?= site_url('admin/shop-closing-days/batch') ?>" class="btn btn-outline-primary">
                <i class="bi bi-plus-circle-dotted"></i>
                一括作成
            </a>
            <a href="<?= site_url('admin/shop-closing-days/new') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                新規作成
            </a>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="page-content">
        
        <?php // フラッシュメッセージ表示 ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php // 検索フォーム ?>
        <div class="search-form">
            <h5 class="mb-3">
                <i class="bi bi-funnel"></i>
                検索・フィルター
            </h5>
            
            <?= form_open('admin/shop-closing-days', ['method' => 'get', 'class' => 'row g-3']) ?>
                <div class="col-md-3">
                    <label for="shop_id" class="form-label">店舗</label>
                    <?= form_dropdown(
                        'shop_id',
                        ['' => '全ての店舗'] + $shops,
                        $filters['shop_id'],
                        ['class' => 'form-select', 'id' => 'shop_id']
                    ) ?>
                </div>
                
                <div class="col-md-3">
                    <label for="repeat_type" class="form-label">繰り返し種別</label>
                    <?= form_dropdown(
                        'repeat_type',
                        ['' => '全ての種別'] + $repeat_type_options,
                        $filters['repeat_type'],
                        ['class' => 'form-select', 'id' => 'repeat_type']
                    ) ?>
                </div>
                
                <div class="col-md-2">
                    <label for="is_active" class="form-label">状態</label>
                    <?= form_dropdown(
                        'is_active',
                        ['' => '全て', '1' => '有効', '0' => '無効'],
                        $filters['is_active'],
                        ['class' => 'form-select', 'id' => 'is_active']
                    ) ?>
                </div>
                
                <div class="col-md-4">
                    <label for="holiday_name" class="form-label">定休日名</label>
                    <?= form_input([
                        'name' => 'holiday_name',
                        'id' => 'holiday_name',
                        'class' => 'form-control',
                        'value' => $filters['holiday_name'],
                        'placeholder' => '部分一致で検索'
                    ]) ?>
                </div>
                
                <div class="col-md-3">
                    <label for="date_from" class="form-label">期間（開始）</label>
                    <?= form_input([
                        'name' => 'date_from',
                        'id' => 'date_from',
                        'type' => 'date',
                        'class' => 'form-control',
                        'value' => $filters['date_from']
                    ]) ?>
                </div>
                
                <div class="col-md-3">
                    <label for="date_to" class="form-label">期間（終了）</label>
                    <?= form_input([
                        'name' => 'date_to',
                        'id' => 'date_to',
                        'type' => 'date',
                        'class' => 'form-control',
                        'value' => $filters['date_to']
                    ]) ?>
                </div>
                
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i>
                        検索
                    </button>
                    <a href="<?= site_url('admin/shop-closing-days') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                        リセット
                    </a>
                </div>
            <?= form_close() ?>
        </div>

        <?php // 結果情報表示 ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="filter-count">
                <?php if (!empty(array_filter($filters))): ?>
                    <i class="bi bi-funnel-fill text-primary"></i>
                    フィルター適用中 - 
                <?php endif; ?>
                全 <?= number_format($total) ?> 件
                <?php if ($total > 0): ?>
                    （<?= $current_page ?> / <?= ceil($total / $per_page) ?> ページ）
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($closing_days)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                該当する定休日データがありません。
            </div>
        <?php else: ?>
            <?php // データテーブル ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>店舗</th>
                            <th>定休日名</th>
                            <th>休業日</th>
                            <th>詳細</th>
                            <th>種別</th>
                            <th>状態</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($closing_days as $closingDay): ?>
                            <tr<?= $closingDay->isToday() ? ' class="table-warning"' : '' ?>>
                                <td>
                                    <?php 
                                    $shop = array_key_exists($closingDay->shop_id, $shops) ? $shops[$closingDay->shop_id] : '不明';
                                    echo esc($shop);
                                    ?>
                                </td>
                                <td>
                                    <strong><?= esc($closingDay->holiday_name) ?></strong>
                                    <?php if ($closingDay->isToday()): ?>
                                        <span class="badge bg-warning text-dark ms-1">本日</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= esc($closingDay->getClosingDateJapanese()) ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= esc($closingDay->getListDescription()) ?>
                                        <?php if (!empty($closingDay->repeat_end_date)): ?>
                                            <br><span class="text-danger">※<?= esc($closingDay->getRepeatEndDateJapanese()) ?>まで</span>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="<?= $closingDay->getRepeatTypeBadgeClass() ?> closing-day-badge">
                                        <?= esc($closingDay->getRepeatTypeName()) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="<?= $closingDay->getActiveStatusBadgeClass() ?> closing-day-badge">
                                        <?= esc($closingDay->getActiveStatusName()) ?>
                                    </span>
                                </td>
                                <td class="table-actions">
                                    <?php if ($closingDay->isEditable()): ?>
                                        <a href="<?= site_url('admin/shop-closing-days/edit/' . $closingDay->id) ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="編集">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($closingDay->isDeletable()): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger"
                                                title="削除"
                                                onclick="confirmDelete(<?= $closingDay->id ?>, '<?= esc($closingDay->holiday_name) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php // ページネーション ?>
            <?php if ($total > $per_page): ?>
                <nav aria-label="ページネーション">
                    <?= $pager->links('default', 'default_full') ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php // 削除確認モーダル ?>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                        削除確認
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong id="deleteTargetName"></strong> を削除しますか？</p>
                    <p class="text-muted">この操作は取り消すことができません。</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <?= form_open('', ['id' => 'deleteForm', 'method' => 'post', 'style' => 'display: inline;']) ?>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i>
                            削除する
                        </button>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('page_specific_scripts') ?>
    <script>
        // 削除確認はJavaScriptファイルで処理されるため、ここでは何もしない
        // 必要に応じてページ固有の初期化処理のみ記述
    </script>
<?= $this->endSection() ?>