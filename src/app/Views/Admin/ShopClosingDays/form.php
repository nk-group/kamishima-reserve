<?= $this->section('content') ?>
    <div class="page-content shop-closing-days-form"><?antml:parameter>
<parameter name="old_str"><?= $this->section('content') ?>
    <div class="page-content"><?= $this->extend('Layouts/admin_layout') ?>

<?= $this->section('title') ?>
    <?= esc($page_title) ?>
<?= $this->endSection() ?>

<?= $this->section('page_specific_head') ?>
    <?php 
    // Body IDを設定（admin.jsで動的インポートに使用）
    $body_id = 'page-admin-shop-closing-days-form';
    ?>
    <style>
        .form-section {
            background: #fff;
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .form-section h5 {
            color: #495057;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }
        .repeat-info {
            background: #f8f9fa;
            border-radius: 0.25rem;
            padding: 0.75rem;
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }
        .form-help {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .required-mark {
            color: #dc3545;
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
            <a href="<?= site_url('admin/shop-closing-days') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                一覧に戻る
            </a>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="page-content">
        
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

        <?php 
        $isEdit = !empty($form_data['id']);
        $formAction = $isEdit 
            ? site_url('admin/shop-closing-days/update/' . $form_data['id']) 
            : site_url('admin/shop-closing-days/create');
        ?>

        <?= form_open($formAction, ['class' => 'needs-validation', 'novalidate' => true]) ?>
            
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
                            'placeholder' => '例: 年末年始休業、毎週火曜日'
                        ]) ?>
                        <?php if (isset($validation) && $validation->hasError('holiday_name')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('holiday_name') ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-help">最大50文字まで入力できます</div>
                    </div>
                </div>
            </div>

            <?php // 日付設定セクション ?>
            <div class="form-section">
                <h5>
                    <i class="bi bi-calendar-event"></i>
                    日付設定
                </h5>
                
                <div class="row">
                    <div class="col-md-4">
                        <label for="closing_date" class="form-label">
                            休業日 <span class="required-mark">*</span>
                        </label>
                        <?= form_input([
                            'name' => 'closing_date',
                            'id' => 'closing_date',
                            'type' => 'date',
                            'class' => 'form-control' . (isset($validation) && $validation->hasError('closing_date') ? ' is-invalid' : ''),
                            'value' => old('closing_date', $form_data['closing_date']),
                            'required' => true
                        ]) ?>
                        <?php if (isset($validation) && $validation->hasError('closing_date')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('closing_date') ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-help">基準となる日付を入力してください</div>
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
                    
                    <div class="col-md-4">
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
                        <div class="form-help">空欄の場合は無期限です</div>
                    </div>
                </div>
                
                <div class="repeat-info" id="repeat-info" style="display: none;">
                    <strong>繰り返し設定について：</strong>
                    <div id="repeat-description"></div>
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
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-<?= $isEdit ? 'pencil-square' : 'plus-lg' ?>"></i>
                    <?= $isEdit ? '更新する' : '登録する' ?>
                </button>
            </div>

        <?= form_close() ?>
    </div>
<?= $this->endSection() ?>

<?= $this->section('page_specific_scripts') ?>
    <script>
        // フォーム処理はJavaScriptファイルで処理されるため、ここでは何もしない
        // 必要に応じてページ固有の初期化処理のみ記述
    </script>
<?= $this->endSection() ?>
