<?= $this->extend('Layouts/admin_layout') ?>

<?= $this->section('title') ?>
    <?= esc($page_title ?? '予約詳細・編集') ?>
<?= $this->endSection() ?>

<?= $this->section('page_header_content') ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-person-circle"></i>
            <?= esc($h1_title) ?>
        </h1>
        <div class="header-info">
            予約受付日 <?= $reservation->created_at ? $reservation->created_at->format('Y年n月j日 H:i') : '不明' ?>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="page-content">
        <?= $this->include('Partials/_alert_messages') ?>

        <?= form_open($form_action, ['id' => 'reservation-form', 'class' => 'needs-validation', 'novalidate' => true]) ?>
            <?= $this->include('Admin/Reservations/_form_fields') ?>
            
            <div class="action-buttons">
                <div class="timestamp-info">
                    <div>新規登録：<?= $reservation->created_at ? $reservation->created_at->format('Y年n月j日 H:i') : '不明' ?></div>
                    <div>最終更新：<?= $reservation->updated_at ? $reservation->updated_at->format('Y年n月j日 H:i') : '不明' ?></div>
                </div>
                <div class="d-flex gap-3">
                    <button type="submit" class="btn-primary-custom">
                        <i class="bi bi-check-circle me-2"></i>保存
                    </button>
                    <a href="<?= route_to('admin.reservations.index') ?>" class="btn-outline-custom">
                        <i class="bi bi-x-circle me-2"></i>キャンセル
                    </a>
                    <button type="button" class="btn-outline-custom" onclick="confirmDelete()">
                        <i class="bi bi-trash me-2"></i>削除
                    </button>
                </div>
            </div>
        <?= form_close() ?>
        
        <!-- 削除用フォーム（隠し） -->
        <?= form_open(route_to('admin.reservations.delete', $reservation->id), ['id' => 'delete-form', 'style' => 'display: none;', 'method' => 'post']) ?>
        <?= form_close() ?>
    </div>
<?= $this->endSection() ?>

<?= $this->section('page_specific_scripts') ?>
<script>
// PHP側のデータをJavaScript側に渡す（シンプル版でテスト）
window.reservationData = {
    timeSlots: <?= json_encode($time_slots) ?>,
    workTypes: <?= json_encode($work_types) ?>,
    currentReservation: <?= json_encode($reservation) ?>
};

// デバッグ用：データが正しく渡されているかチェック
console.log('Edit page: reservationData loaded:', window.reservationData);
console.log('Time slots count:', window.reservationData.timeSlots ? window.reservationData.timeSlots.length : 'undefined');
console.log('Work types count:', window.reservationData.workTypes ? window.reservationData.workTypes.length : 'undefined');
console.log('Current reservation:', window.reservationData.currentReservation);
</script>
<?= $this->endSection() ?>