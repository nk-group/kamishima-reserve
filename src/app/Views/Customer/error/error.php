<?= $this->extend('Layouts/customer_layout') ?>

<?= $this->section('title') ?>
    <?= esc($page_title ?? 'エラー | Clear車検予約') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="main-content">
        <div class="error-page-container">
            <!-- エラーメッセージセクション -->
            <div class="form-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="bi bi-exclamation-triangle-fill section-icon"></i>
                        エラーが発生しました
                    </h2>
                </div>
                <div class="section-body">
                    <div class="error-content">
                        <p class="error-message">
                            <?= esc($error_message ?? 'エラーが発生しました。') ?>
                        </p>
                        <p class="error-help-text">
                            お手数をおかけして申し訳ございません。<br>
                            しばらく時間をおいてから再度お試しいただくか、<br>
                            下記のお電話番号までお問い合わせください。
                        </p>
                    </div>
                    
                    <!-- アクションボタン -->
                    <div class="form-actions-section">
                        <div class="action-buttons">
                            <button type="button" class="btn-primary" onclick="history.back()">
                                <i class="bi bi-arrow-left"></i>
                                前のページに戻る
                            </button>
                            <a href="<?= base_url('customer/calendar/month') ?>" class="btn-secondary">
                                <i class="bi bi-calendar3"></i>
                                カレンダーに戻る
                            </a>
                        </div>
                    </div>
                    
                    <!-- お問い合わせ情報 -->
                    <div class="contact-info">
                        <h3 class="contact-title">
                            <i class="bi bi-telephone-fill"></i>
                            お問い合わせ
                        </h3>
                        <p class="contact-details">
                            <strong>上嶋自動車</strong><br>
                            TEL: <a href="tel:0155-24-2510" class="phone-link">0155-24-2510</a><br>
                            営業時間: 平日 9:00～18:00
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>