<?= $this->extend('Layouts/admin-layout') ?>

<?= $this->section('page_title') ?>
    新規予約作成 | <?= esc(config('App')->appName ?? '車検予約管理システム') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <div class="page-header mb-4">
        <h1>予約入力</h1>
    </div>

    <?php // フラッシュメッセージ等は省略 ?>
    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php $errors = session()->getFlashdata('errors'); if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <p class="mb-1">入力内容にエラーがあります:</p>
            <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?= form_open(site_url('admin/reservations/create'), ['id' => 'new-reservation-form', 'class' => 'form-container']) ?>

        <?php // --- 1行目: 予約番号・状況 (左半分) と 作業種別 (右半分) --- ?>
        <div class="row mb-3 align-items-center gx-3">
            <div class="col-lg-6">
                <div class="d-flex align-items-center form-row-1-left"> <?php // form-row-1-left クラスを適用 (SASSでの調整用) ?>
                    <label class="col-form-label label-fixed-width">予約番号</label> <?php // 固定幅・右寄せ ?>
                    <div class="value-auto-number">
                         <p class="form-control-plaintext py-1 px-2 border rounded bg-light mb-0 h-100 d-flex align-items-center justify-content-center">自動付番</p>
                    </div>
                    <label for="reservation_status_id" class="col-form-label label-status">状況<span class="required-indicator">※</span></label> <?php // SASSで幅と右寄せを調整 (label-fixed-width とは別に) ?>
                    <div class="select-status">
                        <select id="reservation_status_id" name="reservation_status_id" class="form-select" required>
                            <?php foreach ($reservation_statuses as $status): ?>
                                <option value="<?= esc($status['id']) ?>" <?= set_select('reservation_status_id', $status['id'], ($default_reservation_status == $status['id'] && !old('reservation_status_id'))) ?>><?= esc($status['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row align-items-center">
                    <label for="service_type_id" class="col-form-label label-fixed-width">作業種別 <span class="required-indicator">※</span></label>
                    <div class="col">
                        <select id="service_type_id" name="service_type_id" class="form-select" required>
                            <option value="">選択してください</option>
                            <?php foreach ($service_types as $type): ?>
                                <option value="<?= esc($type['id']) ?>" <?= set_select('service_type_id', $type['id']) ?>><?= esc($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <?php // --- 2行目: 予約希望日・時間 (左半分) と 作業店舗 (右半分) --- ?>
        <div class="row mb-3 align-items-center gx-3">
            <div class="col-lg-6">
                 <div class="row align-items-center">
                    <label for="reservation_date" class="col-form-label label-fixed-width">予約希望日 <span class="required-indicator">※</span></label>
                    <div class="col">
                        <div class="d-flex align-items-center reservation-date-time-group">
                            <div class="date-input-wrapper">
                                <input type="date" id="reservation_date" name="reservation_date" class="form-control" value="<?= old('reservation_date', '') ?>" required>
                            </div>
                            <div class="time-input-wrapper ms-2">
                                <input type="time" id="reservation_time_start" name="reservation_time_start" class="form-control" value="<?= old('reservation_time_start', '') ?>" placeholder="開始" aria-label="予約開始時間" required>
                            </div>
                            <span class="mx-2">～</span>
                            <div class="time-input-wrapper">
                                <input type="time" id="reservation_time_end" name="reservation_time_end" class="form-control" value="<?= old('reservation_time_end', '') ?>" placeholder="終了" aria-label="予約終了時間" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row align-items-center">
                    <label for="service_shop_id" class="col-form-label label-fixed-width">作業店舗 <span class="required-indicator">※</span></label>
                    <div class="col">
                        <select id="service_shop_id" name="service_shop_id" class="form-select" required>
                            <option value="">選択してください</option>
                            <?php foreach ($shops as $shop): ?>
                                <option value="<?= esc($shop['id']) ?>" <?= set_select('service_shop_id', $shop['id']) ?>><?= esc($shop['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <?php // --- 3行目: お名前 (左) と フリガナ (右) --- ?>
        <div class="row mb-3">
            <div class="col-lg-6">
                <div class="row align-items-center">
                    <label for="customer_name" class="col-form-label label-fixed-width">お名前 <span class="required-indicator">※</span></label>
                    <div class="col">
                        <input type="text" id="customer_name" name="customer_name" class="form-control" value="<?= old('customer_name', '') ?>" required>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row align-items-center">
                    <label for="customer_kana" class="col-form-label label-fixed-width">フリガナ <span class="required-indicator">※</span></label>
                    <div class="col">
                        <input type="text" id="customer_kana" name="customer_kana" class="form-control" value="<?= old('customer_kana', '') ?>" required>
                    </div>
                </div>
            </div>
        </div>
        
        <?php // --- 4行目: メールアドレス (左) と LINE識別名 (右) --- ?>
        <div class="row mb-3">
            <div class="col-lg-6">
                <div class="row align-items-center">
                    <label for="email" class="col-form-label label-fixed-width">メールアドレス</label>
                    <div class="col">
                        <input type="email" id="email" name="email" class="form-control" placeholder="例: sample@example.com" value="<?= old('email', '') ?>">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row align-items-center">
                    <label class="col-form-label label-fixed-width">LINE識別名</label>
                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="checkbox" name="line_connected" id="line_connected" value="1" <?= old('line_connected') ? 'checked' : '' ?> aria-label="LINE経由の場合チェック">
                                <label class="form-check-label ms-2" for="line_connected">LINE経由</label>
                            </div>
                            <input type="text" name="line_identifier" class="form-control" placeholder="LINE IDなど" value="<?= old('line_identifier', '') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php // --- 5行目: 連絡先電話番号 (左) と 電話番号2 (右) --- ?>
        <div class="row mb-3">
            <div class="col-lg-6">
                <div class="row align-items-center">
                    <label for="phone_number1" class="col-form-label label-fixed-width">連絡先電話番号 <span class="required-indicator">※</span></label>
                    <div class="col">
                        <input type="tel" id="phone_number1" name="phone_number1" class="form-control" placeholder="例: 09012345678" value="<?= old('phone_number1', '') ?>" required>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row align-items-center">
                    <label for="phone_number2" class="col-form-label label-fixed-width">電話番号2</label>
                    <div class="col">
                        <input type="tel" id="phone_number2" name="phone_number2" class="form-control" placeholder="例: 0155123456" value="<?= old('phone_number2', '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <?php // --- 6行目: 郵便番号 (左) --- ?>
        <div class="row mb-3">
            <div class="col-lg-6">
                <div class="row align-items-center">
                    <label for="postal_code" class="col-form-label label-fixed-width">郵便番号</label>
                    <div class="col">
                        <input type="text" id="postal_code" name="postal_code" class="form-control" placeholder="例: 0800803" value="<?= old('postal_code', '') ?>" style="max-width: 150px;">
                    </div>
                </div>
            </div>
            <?php // 右側は空 ?>
        </div>

        <?php // --- 7行目: 住所 (1列幅) --- ?>
        <div class="row mb-3">
            <label for="address" class="col-form-label label-fixed-width">住所</label>
            <div class="col">
                <input type="text" id="address" name="address" class="form-control" placeholder="例: 帯広市東３条南８丁目１－１ ＮＫビル" value="<?= old('address', '') ?>">
            </div>
        </div>

        <?php // --- 8行目: 点線で区切り --- ?>
        <hr class="my-4">

        <?php // --- 9行目: 車両ナンバー (左) と 車種 (右) --- ?>
        <div class="row mb-3">
            <div class="col-lg-6">
                <div class="row align-items-center">
                    <label class="col-form-label label-fixed-width">車両ナンバー <span class="required-indicator">※</span></label>
                    <div class="col">
                        <div class="input-group">
                            <input type="text" name="vehicle_number_region" class="form-control" placeholder="帯広" value="<?= old('vehicle_number_region', '') ?>" required style="flex: 0 1 80px;">
                            <input type="text" name="vehicle_number_class" class="form-control" placeholder="330" value="<?= old('vehicle_number_class', '') ?>" required style="flex: 0 1 70px;">
                            <input type="text" name="vehicle_number_kana" class="form-control" placeholder="る" value="<?= old('vehicle_number_kana', '') ?>" required style="flex: 0 1 60px;">
                            <input type="text" name="vehicle_number_plate" class="form-control" placeholder="583" value="<?= old('vehicle_number_plate', '') ?>" required style="flex: 1 1 auto;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row align-items-center">
                    <label for="car_model" class="col-form-label label-fixed-width">車種 <span class="required-indicator">※</span></label>
                    <div class="col">
                        <input type="text" id="car_model" name="car_model" class="form-control" placeholder="例: スカイライン" value="<?= old('car_model', '') ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <?php // --- 10行目: 車両種別 (左) と 車検満了日 (右) --- ?>
        <div class="row mb-3">
            <div class="col-lg-6">
                <div class="row align-items-center">
                    <label for="vehicle_type_id" class="col-form-label label-fixed-width">車両種別 <span class="required-indicator">※</span></label>
                    <div class="col">
                        <select id="vehicle_type_id" name="vehicle_type_id" class="form-select" required>
                            <option value="">選択してください</option>
                            <?php foreach ($vehicle_types as $type): ?>
                                <option value="<?= esc($type['id']) ?>" <?= set_select('vehicle_type_id', $type['id']) ?>><?= esc($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row align-items-center">
                    <label for="inspection_expiry_date" class="col-form-label label-fixed-width">車検満了日</label>
                    <div class="col">
                        <input type="date" id="inspection_expiry_date" name="inspection_expiry_date" class="form-control" value="<?= old('inspection_expiry_date', '') ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <?php // --- 11行目: (空き) --- ?>
        <div class="row mb-3" style="min-height: 1rem;"></div>

        <?php // --- 12行目(上段): 代車 (左) --- ?>
        <div class="row mb-3">
            <div class="col-lg-6">
                <div class="row align-items-center">
                    <label class="col-form-label label-fixed-width">代車</label>
                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="checkbox" name="courtesy_car_required" id="courtesy_car_required" value="1" <?= old('courtesy_car_required') ? 'checked' : '' ?> aria-label="代車が必要な場合はチェック">
                                <label class="form-check-label ms-2" for="courtesy_car_required">必要</label>
                            </div>
                            <input type="text" name="courtesy_car_detail" class="form-control" placeholder="希望車種など" value="<?= old('courtesy_car_detail', '') ?>">
                        </div>
                    </div>
                </div>
            </div>
             <?php // 右側は空 ?>
        </div>

        <?php // --- 12行目(下段): メモ (1列幅) --- ?>
        <div class="row mb-3">
            <label for="notes" class="col-form-label label-fixed-width">メモ</label>
            <div class="col">
                <textarea id="notes" name="notes" class="form-control" rows="4" placeholder="車両に関する注意事項等を記載します。"><?= old('notes', '') ?></textarea>
            </div>
        </div>

        <?php // --- 13行目: 点線で区切り --- ?>
        <hr class="my-4">

        <div class="form-actions mt-4">
            <button type="submit" class="btn btn-primary">登録する</button>
            <button type="reset" class="btn btn-secondary">クリア</button>
            <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-light">戻る</a>
        </div>
    <?= form_close() ?>

<?= $this->endSection() ?>