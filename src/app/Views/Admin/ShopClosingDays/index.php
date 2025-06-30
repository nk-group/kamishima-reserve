<?= $this->extend('Layouts/admin_layout') ?>

<?= $this->section('title') ?>
    <?= esc($page_title) ?>
<?= $this->endSection() ?>

<?= $this->section('page_header_content') ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-calendar-x"></i>
            <?= esc($h1_title) ?>
        </h1>
        <!-- header-info を削除（件数表示不要） -->
    </div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="page-content shop-closing-days-index">
        
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
            <h5>
                <i class="bi bi-funnel"></i>
                検索条件
            </h5>
            
            <?= form_open('admin/shop-closing-days', ['method' => 'get', 'class' => 'needs-validation', 'novalidate' => true]) ?>
                <div class="row">
                    <div class="col-md-3">
                        <label for="shop_id" class="form-label">店舗</label>
                        <?= form_dropdown(
                            'shop_id',
                            ['' => '-- 全店舗 --'] + $shops,
                            $filters['shop_id'] ?? '',
                            ['class' => 'form-select', 'id' => 'shop_id']
                        ) ?>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="repeat_type" class="form-label">繰り返し種別</label>
                        <?= form_dropdown(
                            'repeat_type',
                            ['' => '-- すべて --'] + $repeat_type_options,
                            $filters['repeat_type'] ?? '',
                            ['class' => 'form-select', 'id' => 'repeat_type']
                        ) ?>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="is_active" class="form-label">状態</label>
                        <?= form_dropdown(
                            'is_active',
                            [
                                '' => '-- すべて --',
                                '1' => '有効',
                                '0' => '無効'
                            ],
                            $filters['is_active'] ?? '',
                            ['class' => 'form-select', 'id' => 'is_active']
                        ) ?>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="holiday_name" class="form-label">定休日名</label>
                        <?= form_input([
                            'name' => 'holiday_name',
                            'id' => 'holiday_name',
                            'class' => 'form-control',
                            'value' => $filters['holiday_name'] ?? '',
                            'placeholder' => '定休日名で部分検索'
                        ]) ?>
                    </div>
                    
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn-search me-2">
                            <i class="bi bi-search"></i>
                            検索
                        </button>
                        <a href="<?= site_url('admin/shop-closing-days') ?>" class="btn-clear">
                            <i class="bi bi-arrow-clockwise"></i>
                            リセット
                        </a>
                    </div>
                </div>
            <?= form_close() ?>
        </div>

        <?php // 検索結果エリア - ここにボタンを配置 ?>
        <div class="results-section">
            <div class="results-header">
                <div class="results-count">
                    <i class="bi bi-funnel-fill"></i>
                    検索結果: <?= number_format($total) ?>件
                    <?php if (!empty(array_filter($filters))): ?>
                        （条件あり）
                    <?php endif; ?>
                </div>
                <div class="results-actions">
                    <a href="<?= site_url('admin/shop-closing-days/batch') ?>" class="btn-create-batch">
                        <i class="bi bi-plus-circle-dotted me-2"></i>一括作成
                    </a>
                    <a href="<?= site_url('admin/shop-closing-days/new') ?>" class="btn-create-new">
                        <i class="bi bi-plus-lg me-2"></i>新規作成
                    </a>
                </div>
            </div>

            <?php // データテーブル ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>店舗名</th>
                            <th>定休日名</th>
                            <th>休業日</th>
                            <th>繰り返し</th>
                            <th>繰り返し終了日</th>
                            <th>状態</th>
                            <th width="140">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($closing_days)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                    検索条件に該当する定休日が見つかりませんでした。
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($closing_days as $closingDay): ?>
                                <tr>
                                    <td>
                                        <?php
                                        // 店舗名を表示（shop_idから店舗名を取得）
                                        $shopName = $shops[$closingDay->shop_id] ?? '不明';
                                        echo esc($shopName);
                                        ?>
                                    </td>
                                    <td><?= esc($closingDay->holiday_name) ?></td>
                                    <td>
                                        <?= format_closing_date_japanese($closingDay->closing_date) ?>
                                    </td>
                                    <td>
                                        <?= get_repeat_type_badge($closingDay->repeat_type) ?>
                                    </td>
                                    <td>
                                        <?php if ($closingDay->repeat_end_date): ?>
                                            <?= format_closing_date_japanese($closingDay->repeat_end_date) ?>
                                        <?php else: ?>
                                            <span class="text-muted">無期限</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= get_active_status_badge($closingDay->is_active) ?>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('admin/shop-closing-days/edit/' . $closingDay->id) ?>" 
                                           class="btn-action btn-edit btn-small"
                                           title="編集">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <?= form_open(
                                            'admin/shop-closing-days/delete/' . $closingDay->id,
                                            [
                                                'style' => 'display: inline;',
                                                'onsubmit' => 'return confirm("この定休日を削除してもよろしいですか？")'
                                            ]
                                        ) ?>
                                            <button type="submit" 
                                                    class="btn-action btn-delete btn-small"
                                                    title="削除">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?= form_close() ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php // ページネーション ?>
            <?php if ($total > $per_page): ?>
                <div class="pagination-container">
                    <?= $pager->links('default', 'default_full') ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
<?= $this->endSection() ?>

<?= $this->section('page_specific_scripts') ?>
    <script>
        // 定休日マスタ一覧のページ固有スクリプトは shop-closing-days.js で処理
    </script>
<?= $this->endSection() ?>