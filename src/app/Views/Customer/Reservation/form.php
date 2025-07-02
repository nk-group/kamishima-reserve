<?= $this->extend('Layouts/customer_layout') ?>

<?= $this->section('title') ?>
    Clear車検予約フォーム | 上嶋自動車
<?= $this->endSection() ?>

<?= $this->section('page_header_content') ?>
    <?php if (!($is_iframe ?? false)): ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-calendar-plus"></i>
            Clear車検予約フォーム
        </h1>
    </div>
    <?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="reservation-form-page">
        <form action="<?= site_url('customer/reservation/submit') ?>" method="post" class="needs-validation" novalidate>
            <?= csrf_field() ?>

            <!-- 予約日時情報 -->
            <div class="form-section">
                <div class="section-header">
                    <h3>予約日時</h3>
                </div>
                <div class="section-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="desired_date" class="form-label">希望日 <span class="required">※</span></label>
                            <input type="date" id="desired_date" name="desired_date" class="form-control" 
                                   value="<?= old('desired_date', $selected_date ?? '') ?>" required>
                            <div class="form-help">予約希望日を選択してください。</div>
                        </div>
                        <div class="form-group">
                            <label for="desired_time_slot_id" class="form-label">希望時間 <span class="required">※</span></label>
                            <select id="desired_time_slot_id" name="desired_time_slot_id" class="form-control" required>
                                <option value="">時間を選択してください</option>
                                <?php if (!empty($available_time_slots) && is_array($available_time_slots)): ?>
                                    <?php foreach ($available_time_slots as $timeSlot): ?>
                                        <option value="<?= esc($timeSlot['id'] ?? '') ?>" 
                                                <?= old('desired_time_slot_id', $selected_time_slot_id ?? '') == ($timeSlot['id'] ?? '') ? 'selected' : '' ?>>
                                            <?= esc($timeSlot['display_time'] ?? '') ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-help">希望する時間帯を選択してください。</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- お客様情報 -->
            <div class="form-section">
                <div class="section-header">
                    <h3>お客様情報</h3>
                </div>
                <div class="section-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="customer_name" class="form-label">お名前 <span class="required">※</span></label>
                            <input type="text" id="customer_name" name="customer_name" class="form-control" 
                                   placeholder="例: 鈴木 一郎" 
                                   value="<?= old('customer_name') ?>" required>
                            <div class="form-help">お名前をフルネームで入力してください。</div>
                        </div>
                        <div class="form-group">
                            <label for="customer_kana" class="form-label">フリガナ</label>
                            <input type="text" id="customer_kana" name="customer_kana" class="form-control" 
                                   placeholder="例: スズキ イチロウ" 
                                   value="<?= old('customer_kana') ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email" class="form-label">メールアドレス <span class="required">※</span></label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   placeholder="例: suzuki@example.com" 
                                   value="<?= old('email') ?>" required>
                            <div class="form-help">予約確認メールを送信いたします。</div>
                        </div>
                        <div class="form-group">
                            <label for="phone_number1" class="form-label">電話番号 <span class="required">※</span></label>
                            <input type="tel" id="phone_number1" name="phone_number1" class="form-control" 
                                   placeholder="例: 090-1234-5678" 
                                   value="<?= old('phone_number1') ?>" required>
                            <div class="form-help">連絡のつきやすい電話番号をご入力ください。</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 車両情報 -->
            <div class="form-section">
                <div class="section-header">
                    <h3>車両情報</h3>
                </div>
                <div class="section-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">車両ナンバー <span class="required">※</span></label>
                            <div class="vehicle-number-group">
                                <div class="vehicle-number-item region">
                                    <label class="form-label">地域</label>
                                    <input type="text" name="vehicle_license_region" class="form-control" 
                                           placeholder="帯広" value="<?= old('vehicle_license_region') ?>">
                                </div>
                                <div class="vehicle-number-item class">
                                    <label class="form-label">分類番号</label>
                                    <input type="text" name="vehicle_license_class" class="form-control" 
                                           placeholder="330" value="<?= old('vehicle_license_class') ?>">
                                </div>
                                <div class="vehicle-number-item kana">
                                    <label class="form-label">ひらがな</label>
                                    <input type="text" name="vehicle_license_kana" class="form-control" 
                                           placeholder="る" value="<?= old('vehicle_license_kana') ?>">
                                </div>
                                <div class="vehicle-number-item number">
                                    <label class="form-label">一連指定番号 <span class="required">※</span></label>
                                    <input type="text" name="vehicle_license_number" class="form-control" 
                                           placeholder="583" value="<?= old('vehicle_license_number') ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="vehicle_model_name" class="form-label">車種 <span class="required">※</span></label>
                            <input type="text" id="vehicle_model_name" name="vehicle_model_name" class="form-control" 
                                   placeholder="例: スカイライン" 
                                   value="<?= old('vehicle_model_name') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="first_registration_date" class="form-label">初年度登録</label>
                            <input type="month" id="first_registration_date" name="first_registration_date" class="form-control" 
                                   value="<?= old('first_registration_date') ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="shaken_expiration_date" class="form-label">車検満了日</label>
                            <input type="date" id="shaken_expiration_date" name="shaken_expiration_date" class="form-control" 
                                   value="<?= old('shaken_expiration_date') ?>">
                        </div>
                        <div class="form-group">
                            <label for="vehicle_type_id" class="form-label">車両種別</label>
                            <select id="vehicle_type_id" name="vehicle_type_id" class="form-control">
                                <option value="">選択してください</option>
                                <?php if (!empty($vehicle_types) && is_array($vehicle_types)): ?>
                                    <?php foreach ($vehicle_types as $vehicleType): ?>
                                        <option value="<?= esc($vehicleType['id'] ?? '') ?>" 
                                                <?= old('vehicle_type_id') == ($vehicleType['id'] ?? '') ? 'selected' : '' ?>>
                                            <?= esc($vehicleType['name'] ?? '') ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="model_spec_number" class="form-label">型式指定番号</label>
                            <input type="text" id="model_spec_number" name="model_spec_number" class="form-control" 
                                   placeholder="例: 50506" value="<?= old('model_spec_number') ?>">
                        </div>
                        <div class="form-group">
                            <label for="classification_number" class="form-label">類別区分番号</label>
                            <input type="text" id="classification_number" name="classification_number" class="form-control" 
                                   placeholder="例: 0689" value="<?= old('classification_number') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ご要望等 -->
            <div class="form-section">
                <div class="section-header">
                    <h3>ご要望等</h3>
                </div>
                <div class="section-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="notes" class="form-label">ご要望・備考</label>
                            <textarea id="notes" name="notes" class="form-control" rows="4" 
                                      placeholder="車検に関するご要望や気になる点がございましたらご記入ください。"><?= old('notes') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 送信ボタン -->
            <div class="form-actions-section">
                <div class="action-buttons">
                    <button type="reset" class="btn-reset">
                        <i class="bi bi-arrow-clockwise"></i>
                        入力内容をリセット
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-check-circle"></i>
                        予約を申し込む
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php // JavaScript用データを非表示で設定 ?>
    <div id="reservation-form-data" style="display: none;">
    </div>
<?= $this->endSection() ?>