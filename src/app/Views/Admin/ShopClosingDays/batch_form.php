<?= $this->extend('Layouts/admin_layout') ?>

<?= $this->section('title') ?>
    <?= esc($page_title) ?>
<?= $this->endSection() ?>

<?= $this->section('page_specific_head') ?>
    <?php 
    // Body IDを設定（admin.jsで動的インポートに使用）
    $body_id = 'page-admin-shop-closing-days-batch';
    ?>
<?= $this->endSection() ?>

<?= $this->section('page_header_content') ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-plus-circle-dotted"></i>
            <?= esc($h1_title) ?>
        </h1>
        <div class="header-actions">
            <a href="<?= site_url('admin/shop-closing-days') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                一覧に戻る
            </a>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="page-content shop-closing-days-batch">
        
        <div class="batch-info">
            <h6><i class="bi bi-info-circle"></i> 一括作成について</h6>
            <p class="mb-0">
                指定した期間内の全ての日に対して、同じ名前の定休日を一括で登録できます。<br>
                年末年始休業やお盆休み等の連続した休業期間の登録に便利です。
            </p>
        </div>

        <?php // バリデーションエラー表示 ?>
        <?php if (isset($validation) && $validation->getErrors()): ?>
            <div class="alert alert-danger">
                <h6><i class="bi bi-exclamation-triangle-fill"></i> 入力エラーがあります</h6>
                <ul class="mb-0">
                    <?php foreach ($validation->getErrors() as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= form_open('admin/shop-closing-days/batch-create', ['class' => 'needs-validation', 'novalidate' => true]) ?>
            
            <?php // 基本情報セクション ?>
            <div class="form-section">
                <h5>
                    <i class="bi bi-info-circle"></i>
                    基本情報
                </h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <label for="shop_id" class="form-label">
                            店舗 <span class="required-mark">*</span>
                        </label>
                        <?= form_dropdown(
                            'shop_id',
                            ['' => '-- 店舗を選択してください --'] + $shops,
                            old('shop_id', $form_data['shop_id']),
                            [
                                'class' => 'form-select' . (isset($validation) && $validation->hasError('shop_id') ? ' is-invalid' : ''),
                                'id' => 'shop_id',
                                'required' => true
                            ]
                        ) ?>
                        <?php if (isset($validation) && $validation->hasError('shop_id')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('shop_id') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="holiday_name" class="form-label">
                            定休日名 <span class="required-mark">*</span>
                        </label>
                        <?= form_input([
                            'name' => 'holiday_name',
                            'id' => 'holiday_name',
                            'class' => 'form-control' . (isset($validation) && $validation->hasError('holiday_name') ? ' is-invalid' : ''),
                            'value' => old('holiday_name', $form_data['holiday_name']),
                            'maxlength' => 50,
                            'required' => true,
                            'placeholder' => '例: 年末年始休業、お盆休み'
                        ]) ?>
                        <?php if (isset($validation) && $validation->hasError('holiday_name')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('holiday_name') ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-help">全ての日に同じ名前が適用されます</div>
                    </div>
                </div>
            </div>

            <?php // 期間設定セクション ?>
            <div class="form-section">
                <h5>
                    <i class="bi bi-calendar-range"></i>
                    期間設定
                </h5>
                
                <div class="row">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">
                            開始日 <span class="required-mark">*</span>
                        </label>
                        <?= form_input([
                            'name' => 'start_date',
                            'id' => 'start_date',
                            'type' => 'date',
                            'class' => 'form-control' . (isset($validation) && $validation->hasError('start_date') ? ' is-invalid' : ''),
                            'value' => old('start_date', $form_data['start_date']),
                            'required' => true
                        ]) ?>
                        <?php if (isset($validation) && $validation->hasError('start_date')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('start_date') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">
                            終了日 <span class="required-mark">*</span>
                        </label>
                        <?= form_input([
                            'name' => 'end_date',
                            'id' => 'end_date',
                            'type' => 'date',
                            'class' => 'form-control' . (isset($validation) && $validation->hasError('end_date') ? ' is-invalid' : ''),
                            'value' => old('end_date', $form_data['end_date']),
                            'required' => true
                        ]) ?>
                        <?php if (isset($validation) && $validation->hasError('end_date')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('end_date') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="repeat_type" class="form-label">
                            繰り返し種別 <span class="required-mark">*</span>
                        </label>
                        <?= form_dropdown(
                            'repeat_type',
                            $repeat_type_options,
                            old('repeat_type', $form_data['repeat_type']),
                            [
                                'class' => 'form-select' . (isset($validation) && $validation->hasError('repeat_type') ? ' is-invalid' : ''),
                                'id' => 'repeat_type',
                                'required' => true
                            ]
                        ) ?>
                        <?php if (isset($validation) && $validation->hasError('repeat_type')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('repeat_type') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="repeat_end_date" class="form-label">
                            繰り返し終了日
                        </label>
                        <?= form_input([
                            'name' => 'repeat_end_date',
                            'id' => 'repeat_end_date',
                            'type' => 'date',
                            'class' => 'form-control' . (isset($validation) && $validation->hasError('repeat_end_date') ? ' is-invalid' : ''),
                            'value' => old('repeat_end_date', $form_data['repeat_end_date'])
                        ]) ?>
                        <?php if (isset($validation) && $validation->hasError('repeat_end_date')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('repeat_end_date') ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-help">空欄の場合は無期限で繰り返されます</div>
                    </div>
                </div>

                <div class="preview-info" id="preview-info" style="display: none;">
                    <strong>登録プレビュー：</strong>
                    <div id="preview-description"></div>
                </div>
            </div>

            <?php // その他設定セクション ?>
            <div class="form-section">
                <h5>
                    <i class="bi bi-gear"></i>
                    その他設定
                </h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <label for="is_active" class="form-label">状態</label>
                        <div class="form-check form-switch">
                            <?= form_checkbox([
                                'name' => 'is_active',
                                'id' => 'is_active',
                                'value' => '1',
                                'checked' => old('is_active', $form_data['is_active']) == 1,
                                'class' => 'form-check-input'
                            ]) ?>
                            <label class="form-check-label" for="is_active">
                                有効にする
                            </label>
                        </div>
                        <div class="form-help">無効にすると休業日として適用されません</div>
                    </div>
                </div>
            </div>

            <?php // 送信ボタン ?>
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/shop-closing-days') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                    キャンセル
                </a>
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <i class="bi bi-plus-circle-dotted"></i>
                    一括登録する
                </button>
            </div>

        <?= form_close() ?>
    </div>
<?= $this->endSection() ?>

<?= $this->section('page_specific_scripts') ?>
    <script>
        // 一括作成処理はJavaScriptファイルで処理されるため、ここでは何もしない
        // 必要に応じてページ固有の初期化処理のみ記述
    </script>
<?= $this->endSection() ?>