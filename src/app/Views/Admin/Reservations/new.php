<?= $this->extend('Layouts/admin_layout') ?>

<?= $this->section('title') ?>
    <?= esc($page_title ?? '新規予約作成') ?>
<?= $this->endSection() ?>

<?= $this->section('page_header_content') ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-calendar-plus"></i>
            <?= esc($h1_title) ?>
        </h1>
    </div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="page-content">
        <?= $this->include('Partials/_alert_messages') ?>

        <?= form_open($form_action, ['id' => 'reservation-form', 'class' => 'needs-validation', 'novalidate' => true]) ?>
            <?= $this->include('Admin/Reservations/_form_fields') ?>
            
            <div class="action-buttons">
                <div></div> <!-- 左側は空 -->
                <div class="d-flex gap-3">
                    <button type="submit" class="btn-primary-custom">
                        <i class="bi bi-check-circle me-2"></i>登録
                    </button>
                    <button type="reset" class="btn-reset">
                        <i class="bi bi-arrow-clockwise me-2"></i>クリア
                    </button>
                    <a href="<?= route_to('admin.reservations.index') ?>" class="btn-outline-custom">
                        <i class="bi bi-x-circle me-2"></i>キャンセル
                    </a>
                </div>
            </div>
        <?= form_close() ?>
    </div>
<?= $this->endSection() ?>

<?= $this->section('page_specific_scripts') ?>
<script>
// PHP側のデータをJavaScript側に渡す（初期値対応版）
window.reservationData = {
    timeSlots: <?= json_encode($time_slots) ?>,
    workTypes: <?= json_encode($work_types) ?>,
    currentReservation: null,
    initialValues: <?= json_encode($initial_values ?? []) ?>
};

// 初期値の設定
document.addEventListener('DOMContentLoaded', function() {
    const initialValues = window.reservationData.initialValues || {};
    
    // 予約希望日の初期値設定
    if (initialValues.desired_date) {
        const desiredDateField = document.getElementById('desired_date');
        if (desiredDateField) {
            desiredDateField.value = initialValues.desired_date;
            
            // Flatpickrが初期化されている場合は、そちらにも値を設定
            if (desiredDateField._flatpickr) {
                desiredDateField._flatpickr.setDate(initialValues.desired_date);
            }
        }
    }
    
    // 作業店舗の初期値設定
    if (initialValues.shop_id) {
        const shopField = document.getElementById('shop_id');
        if (shopField) {
            shopField.value = initialValues.shop_id;
            
            // selectの変更イベントを発火（時間帯の絞り込み等のため）
            const changeEvent = new Event('change', { bubbles: true });
            shopField.dispatchEvent(changeEvent);
        }
    }
    
    // 作業種別の初期値設定
    if (initialValues.work_type_id) {
        const workTypeField = document.getElementById('work_type_id');
        if (workTypeField) {
            workTypeField.value = initialValues.work_type_id;
            
            // selectの変更イベントを発火（既存の連携処理のため）
            const changeEvent = new Event('change', { bubbles: true });
            workTypeField.dispatchEvent(changeEvent);
        }
    }
    
    // LINE経由フラグの初期値設定
    if (initialValues.hasOwnProperty('via_line')) {
        const viaLineField = document.getElementById('via_line');
        if (viaLineField) {
            viaLineField.checked = initialValues.via_line;
        }
    }
});
</script>
<?= $this->endSection() ?>