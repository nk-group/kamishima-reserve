<?php
// 編集モード判定とデフォルト値設定
$reservation = $reservation ?? null;
$isEdit = $is_edit ?? false;

// old()とエンティティデータの優先順位を考慮したヘルパー関数
function getFieldValue($fieldName, $reservation = null, $default = '') {
    $oldValue = old($fieldName);
    if ($oldValue !== null) {
        return $oldValue;
    }
    
    if ($reservation && isset($reservation->$fieldName)) {
        return $reservation->$fieldName;
    }
    
    return $default;
}
?>

<!-- Basic Information -->
<div class="form-section">
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">予約番号</label>
            <?php if ($isEdit && $reservation): ?>
                <input type="text" class="form-control" value="<?= esc($reservation->reservation_no) ?>" readonly>
            <?php else: ?>
                <div class="auto-number-display">自動付番</div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="reservation_status_id" class="form-label">状況 <span class="text-danger">※</span></label>
            <select id="reservation_status_id" name="reservation_status_id" class="form-select" required>
                <option value="">選択してください</option>
                <?php foreach ($reservation_statuses as $statusId => $statusName): ?>
                    <option value="<?= esc($statusId) ?>" 
                        <?= set_select('reservation_status_id', $statusId, 
                            getFieldValue('reservation_status_id', $reservation, $default_reservation_status) == $statusId) ?>>
                        <?= esc($statusName) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="work_type_id" class="form-label">作業種別 <span class="text-danger">※</span></label>
            <select id="work_type_id" name="work_type_id" class="form-select" required>
                <option value="">選択してください</option>
                <?php foreach ($work_types as $workType): ?>
                    <option value="<?= esc($workType->id) ?>" 
                        <?= set_select('work_type_id', $workType->id, 
                            getFieldValue('work_type_id', $reservation) == $workType->id) ?>>
                        <?= esc($workType->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="desired_date" class="form-label">予約希望日 <span class="text-danger">※</span></label>
            <?= flatpickr_input('desired_date', getFieldValue('desired_date', $reservation), ['required' => true], 'date') ?>
        </div>
        
        <!-- 時間帯選択（Clear車検用） -->
        <div class="form-group time-slot-group" style="display: none;">
            <label for="desired_time_slot_id" class="form-label">時間帯</label>
            <select id="desired_time_slot_id" name="desired_time_slot_id" class="form-select">
                <option value="">選択してください</option>
            </select>
        </div>
        
        <!-- 直接時刻入力（その他作業用） -->
        <div class="form-group time-input-group">
            <label class="form-label">時間</label>
            <div class="time-inputs">
                <input type="time" id="reservation_start_time" name="reservation_start_time" 
                       class="form-control" value="<?= esc(getFieldValue('reservation_start_time', $reservation)) ?>">
                <span class="time-separator">～</span>
                <input type="time" id="reservation_end_time" name="reservation_end_time" 
                       class="form-control" value="<?= esc(getFieldValue('reservation_end_time', $reservation)) ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="shop_id" class="form-label">作業店舗 <span class="text-danger">※</span></label>
            <select id="shop_id" name="shop_id" class="form-select" required>
                <option value="">選択してください</option>
                <?php foreach ($shops as $shop): ?>
                    <option value="<?= esc($shop->id) ?>" 
                        <?= set_select('shop_id', $shop->id, 
                            getFieldValue('shop_id', $reservation) == $shop->id) ?>>
                        <?= esc($shop->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<!-- Customer Information -->
<div class="form-section">
    <div class="form-row">
        <div class="form-group">
            <label for="customer_name" class="form-label">お名前 <span class="text-danger">※</span></label>
            <input type="text" id="customer_name" name="customer_name" class="form-control" 
                   value="<?= esc(getFieldValue('customer_name', $reservation)) ?>" required>
        </div>
        <div class="form-group">
            <label for="customer_kana" class="form-label">カナ名</label>
            <input type="text" id="customer_kana" name="customer_kana" class="form-control" 
                   value="<?= esc(getFieldValue('customer_kana', $reservation)) ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="email" class="form-label">メールアドレス <span class="text-danger">※</span></label>
            <div class="input-group">
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="例: sample@example.com" 
                       value="<?= esc(getFieldValue('email', $reservation)) ?>" required>
                <span class="input-group-text">
                    <i class="bi bi-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" 
                       title="このメールアドレスに予約確認・リマインドメールが送信されます。"></i>
                </span>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">LINE識別名</label>
            <div class="d-flex align-items-center gap-3">
                <label for="via_line" class="line-tag-container" style="cursor: pointer;">
                    <input type="checkbox" class="line-checkbox" id="via_line" name="via_line" value="1" 
                           <?= getFieldValue('via_line', $reservation) ? 'checked' : '' ?>>
                    <span class="line-tag-text">LINE経由</span>
                </label>
                <input type="text" name="line_display_name" class="form-control line-text-input" 
                       placeholder="LINE IDなど" 
                       value="<?= esc(getFieldValue('line_display_name', $reservation)) ?>" 
                       id="line_display_name">
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="phone_number1" class="form-label">連絡先電話番号 <span class="text-danger">※</span></label>
            <input type="tel" id="phone_number1" name="phone_number1" class="form-control" 
                   placeholder="例: 09012345678" 
                   value="<?= esc(getFieldValue('phone_number1', $reservation)) ?>" required>
        </div>
        <div class="form-group">
            <label for="phone_number2" class="form-label">電話番号２</label>
            <input type="tel" id="phone_number2" name="phone_number2" class="form-control" 
                   placeholder="例: 0155123456" 
                   value="<?= esc(getFieldValue('phone_number2', $reservation)) ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group narrow">
            <label for="postal_code" class="form-label">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code" class="form-control" 
                   placeholder="例: 0800803" 
                   value="<?= esc(getFieldValue('postal_code', $reservation)) ?>">
        </div>
        <div class="form-group wide">
            <label for="address" class="form-label">住所</label>
            <input type="text" id="address" name="address" class="form-control" 
                   placeholder="例: 帯広市東３条南８丁目１－１ ＮＫビル" 
                   value="<?= esc(getFieldValue('address', $reservation)) ?>">
        </div>
    </div>
</div>

<hr class="section-divider">

<!-- Vehicle Information -->
<div class="form-section">
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">車両ナンバー <span class="text-danger">※</span></label>
            <div class="vehicle-number-group">
                <input type="text" name="vehicle_license_region" class="form-control" placeholder="帯広" 
                       value="<?= esc(getFieldValue('vehicle_license_region', $reservation)) ?>" style="width: 80px;">
                <input type="text" name="vehicle_license_class" class="form-control" placeholder="330" 
                       value="<?= esc(getFieldValue('vehicle_license_class', $reservation)) ?>" style="width: 60px;">
                <input type="text" name="vehicle_license_kana" class="form-control" placeholder="る" 
                       value="<?= esc(getFieldValue('vehicle_license_kana', $reservation)) ?>" style="width: 50px;">
                <input type="text" name="vehicle_license_number" class="form-control" placeholder="583" 
                       value="<?= esc(getFieldValue('vehicle_license_number', $reservation)) ?>" style="width: 80px;" required>
            </div>
        </div>
        <div class="form-group">
            <label for="vehicle_model_name" class="form-label">車種 <span class="text-danger">※</span></label>
            <input type="text" id="vehicle_model_name" name="vehicle_model_name" class="form-control" 
                   placeholder="例: スカイライン" 
                   value="<?= esc(getFieldValue('vehicle_model_name', $reservation)) ?>" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="first_registration_date" class="form-label">初年度登録</label>
            <?= flatpickr_input('first_registration_date', getFieldValue('first_registration_date', $reservation), [], 'month') ?>
        </div>
        <div class="form-group">
            <label for="shaken_expiration_date" class="form-label">車検満了日</label>
            <?= flatpickr_input('shaken_expiration_date', getFieldValue('shaken_expiration_date', $reservation), [], 'date') ?>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="model_spec_number" class="form-label">型式指定番号</label>
            <input type="text" id="model_spec_number" name="model_spec_number" class="form-control" 
                   placeholder="例: 50506" 
                   value="<?= esc(getFieldValue('model_spec_number', $reservation)) ?>">
        </div>
        <div class="form-group">
            <label for="classification_number" class="form-label">類別区分番号</label>
            <input type="text" id="classification_number" name="classification_number" class="form-control" 
                   placeholder="例: 0689" 
                   value="<?= esc(getFieldValue('classification_number', $reservation)) ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label class="form-label">代車</label>
            <div class="checkbox-group">
                <input type="checkbox" class="form-check-input" id="loaner_usage" name="loaner_usage" value="1"
                       <?= getFieldValue('loaner_usage', $reservation) ? 'checked' : '' ?>>
                <label for="loaner_usage" class="form-check-label">必要</label>
                <input type="text" id="loaner_name" name="loaner_name" class="form-control ms-3" 
                       placeholder="代車名" style="max-width: 200px;"
                       value="<?= esc(getFieldValue('loaner_name', $reservation)) ?>">
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="customer_requests" class="form-label">お客様要望</label>
            <textarea id="customer_requests" name="customer_requests" class="form-control textarea-customer-request" 
                      placeholder="お客様からのご要望など"><?= esc(getFieldValue('customer_requests', $reservation)) ?></textarea>
        </div>
    </div>
</div>

<!-- Notes Section -->
<div class="form-section">
    <div class="form-row">
        <div class="form-group">
            <label for="notes" class="form-label">メモ</label>
            <textarea id="notes" name="notes" class="form-control textarea-memo" 
                      placeholder="車検に関する注意事項等を記載します。"><?= esc(getFieldValue('notes', $reservation)) ?></textarea>
        </div>
    </div>
    
    <!-- 予約確認用URL（修正画面のみ表示） -->
    <?php if ($isEdit && $reservation && !empty($reservation->reservation_guid)): ?>
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">予約確認用URL</label>
            <div class="input-group">
                <input type="text" class="form-control" 
                       value="<?= base_url('customer/reservations/' . esc($reservation->reservation_guid)) ?>" readonly>
                <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard(this.previousElementSibling)">
                    <i class="bi bi-clipboard"></i> コピー
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Inspection Section -->
<div class="inspection-section">
    <h3 class="inspection-title">
        <i class="bi bi-gear-fill"></i>
        点検後に入力
    </h3>
    <p class="text-muted mb-3">この情報は点検後に整備士が入力する情報です。</p>

    <div class="form-row">
        <div class="form-group">
            <label for="next_inspection_date" class="form-label">次回点検日</label>
            <?= flatpickr_input('next_inspection_date', getFieldValue('next_inspection_date', $reservation), [], 'date') ?>
        </div>
        <div class="form-group">
            <label for="next_work_type_id" class="form-label">次回作業種別</label>
            <select id="next_work_type_id" name="next_work_type_id" class="form-select">
                <option value="">選択してください</option>
                <?php foreach ($work_types as $workType): ?>
                    <option value="<?= esc($workType->id) ?>" 
                        <?= set_select('next_work_type_id', $workType->id, 
                            getFieldValue('next_work_type_id', $reservation) == $workType->id) ?>>
                        <?= esc($workType->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label class="form-label">次回点検案内</label>
            <div class="checkbox-group">
                <input type="checkbox" class="form-check-input" id="send_inspection_notice" 
                       name="send_inspection_notice" value="1" 
                       <?= getFieldValue('send_inspection_notice', $reservation) ? 'checked' : '' ?>>
                <label for="send_inspection_notice" class="form-check-label">次回点検案内を送る</label>
            </div>
            <div class="btn-group-custom">
                <button type="button" class="btn btn-month" data-months="12">12か月後</button>
                <button type="button" class="btn btn-month" data-months="24">24か月後</button>
            </div>
        </div>
        <div class="form-group">
            <label for="next_contact_date" class="form-label">次回連絡日</label>
            <?= flatpickr_input('next_contact_date', getFieldValue('next_contact_date', $reservation), [], 'date') ?>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" class="form-check-input" id="inspection_notice_sent" 
                       name="inspection_notice_sent" value="1" 
                       <?= getFieldValue('inspection_notice_sent', $reservation) ? 'checked' : '' ?>>
                <label for="inspection_notice_sent" class="form-check-label">リマインドメール送信済</label>
                <?php if ($isEdit && $reservation && $reservation->inspection_notice_sent): ?>
                    <span class="ms-3 text-muted">送信日：<?= $reservation->updated_at ? $reservation->updated_at->format('Y年m月d日 H:i') : '不明' ?></span>
                <?php else: ?>
                    <span class="ms-3 text-muted">送信日：----年-月-日 --:--</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// URLコピー機能
function copyToClipboard(inputElement) {
    inputElement.select();
    inputElement.setSelectionRange(0, 99999); // モバイル対応
    
    try {
        document.execCommand('copy');
        
        // ボタンのテキストを一時的に変更
        const button = inputElement.nextElementSibling;
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check"></i> コピー済み';
        button.classList.add('btn-success');
        button.classList.remove('btn-outline-secondary');
        
        setTimeout(() => {
            button.innerHTML = originalHtml;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    } catch (err) {
        console.error('URLのコピーに失敗しました:', err);
    }
}
</script>