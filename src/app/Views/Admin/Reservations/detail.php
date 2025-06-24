<?= $this->extend('Layouts/admin_layout') // 管理者向け共通レイアウトを継承 ?>

<?php // ページヘッダーセクション ?>
<?= $this->section('page_header_content') ?>
<?= $this->section('title') // レイアウトの <title> タグに値を設定 ?>
    <?= esc($page_title ?? '予約詳細') ?>
<?= $this->endSection() ?>

    <?php // reservations-detail.html の .page-header 構造を移植 ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-person-circle"></i>
            <?= esc($h1_title) ?>
        </h1>
        <div class="header-info">
            予約受付日 2025年1月30日 20:15
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('content') // レイアウトのメインコンテンツ部分 ?>
    <?php // reservations-detail.html の .form-content 構造を移植 ?>
    <div class="page-content">
        <!-- Basic Information -->
        <div class="form-section">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">予約番号</label>
                    <input type="text" class="form-control" value="00010375" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">状況</label>
                    <select class="form-select">
                        <option selected>予約確定</option>
                        <option>未確定</option>
                        <option>作業完了</option>
                        <option>キャンセル</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">作業種別</label>
                    <select class="form-select">
                        <option selected>Clear車検</option>
                        <option>定期点検</option>
                        <option>一般車検</option>
                        <option>一般整備</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">予約希望日 <span class="text-danger">※</span></label>
                    <input type="text" class="form-control flatpickr-date" value="2025/2/5水" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">時間</label>
                    <div class="time-inputs">
                        <input type="time" class="form-control" value="09:30">
                        <span class="time-separator">～</span>
                        <input type="time" class="form-control" value="09:30">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">作業店舗</label>
                    <select class="form-select">
                        <option selected>本社（車検・整備工場）</option>
                        <option>Clear車検</option>
                        <option>モーターショップカミシマ</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="form-section">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">お名前 <span class="text-danger">※</span></label>
                    <input type="text" class="form-control" value="鈴木　一郎">
                </div>
                <div class="form-group">
                    <label class="form-label">カナ名</label>
                    <input type="text" class="form-control" value="スズキ　イチロウ">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">メールアドレス <span class="text-danger">※</span></label>
                    <div class="input-group">
                        <input type="email" class="form-control" value="i.suzuki@sample.com">
                        <span class="input-group-text">
                            <i class="bi bi-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="このメールアドレスに予約確認・リマインドメールが送信されます。"></i>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">LINE識別名</label>
                    <div class="d-flex align-items-center gap-3">
                        <label for="lineVia" class="line-tag-container" style="cursor: pointer;">
                            <input type="checkbox" class="line-checkbox" id="lineVia" checked>
                            <span class="line-tag-text">LINE経由</span>
                        </label>
                        <input type="text" class="form-control line-text-input" value="鈴木" id="lineDisplayName">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">連絡先電話番号 <span class="text-danger">※</span></label>
                    <input type="tel" class="form-control" value="090-XXXX-XXXX">
                </div>
                <div class="form-group">
                    <label class="form-label">電話番号２</label>
                    <input type="tel" class="form-control" value="0155-XX-XXXX">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group narrow">
                    <label class="form-label">郵便番号</label>
                    <input type="text" class="form-control" value="080-0803">
                </div>
                <div class="form-group wide">
                    <label class="form-label">住所</label>
                    <input type="text" class="form-control" value="帯広市東３条南８丁目１－１　ＮＫビル">
                </div>
            </div>
        </div>

        <hr class="section-divider">

        <!-- Vehicle Information -->
        <div class="form-section">
            <hr class="section-divider">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">車両ナンバー <span class="text-danger">※</span></label>
                    <div class="vehicle-number-group">
                        <input type="text" class="form-control" value="帯広" style="width: 80px;">
                        <input type="text" class="form-control" value="330" style="width: 60px;">
                        <input type="text" class="form-control" value="る" style="width: 50px;">
                        <input type="text" class="form-control" value="583" style="width: 80px;">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">車種 <span class="text-danger">※</span></label>
                    <input type="text" class="form-control" value="スカイライン">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">初年度登録</label>
                    <input type="text" class="form-control flatpickr-month" value="2025年2月">
                </div>
                <div class="form-group">
                    <label class="form-label">車検満了日 <span class="text-danger">※</span></label>
                    <input type="text" class="form-control flatpickr-date" value="2025年2月20日">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">型式指定番号 <span class="text-danger">※</span></label>
                    <input type="text" class="form-control" value="50506">
                </div>
                <div class="form-group">
                    <label class="form-label">類別区分番号 <span class="text-danger">※</span></label>
                    <input type="text" class="form-control" value="0689">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">代車</label>
                    <div class="checkbox-group">
                        <input type="checkbox" class="form-check-input" id="loaner" checked>
                        <label for="loaner" class="form-check-label">必要</label>
                        <input type="text" class="form-control ms-3" value="フィット1号車" style="max-width: 200px;">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">お客様要望</label>
                    <textarea class="form-control textarea-customer-request" placeholder="お客様からのご要望など"></textarea>
                </div>
            </div>
        </div>

        <!-- Notes Section -->
        <div class="form-section">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">メモ</label>
                    <textarea class="form-control textarea-memo" placeholder="車検に関する注意事項等を記載します。"></textarea>
                </div>
            </div>
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
                    <label class="form-label">次回車検日</label>
                    <input type="text" class="form-control flatpickr-date" value="2025年2月20日" id="nextInspectionDate">
                </div>
                <div class="form-group">
                    <label class="form-label">次回点検案内</label>
                    <div class="checkbox-group">
                        <input type="checkbox" class="form-check-input" id="nextInspection" checked>
                        <label for="nextInspection" class="form-check-label">次回点検案内を送る</label>
                    </div>
                    <div class="btn-group-custom">
                        <button type="button" class="btn btn-month" data-months="12">12か月後</button>
                        <button type="button" class="btn btn-month" data-months="24">24か月後</button>
                    </div>
                    <select class="form-select">
                        <option selected>Clear車検</option>
                        <option>定期点検</option>
                        <option>一般車検</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" class="form-check-input" id="reminderEmail" checked>
                        <label for="reminderEmail" class="form-check-label">リマインドメール送信済</label>
                        <span class="ms-3 text-muted">送信日：----年-月-日 --:--</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <div class="timestamp-info">
                <div>新規登録：2025年1月30日 20:15</div>
                <div>最終更新：2025年1月30日 20:15</div>
            </div>
            <div class="d-flex gap-3"> <?php // btn-primary-custom, btn-outline-custom, btn-month は _buttons.scss で定義 ?>
                <button type="button" class="btn-primary-custom">
                    <i class="bi bi-check-circle me-2"></i>保存
                </button>
                <a href="<?= route_to('admin.reservations.index') ?>" class="btn-outline-custom">
                    <i class="bi bi-x-circle me-2"></i>キャンセル
                </a>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>