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
// PHP側のデータをJavaScript側に渡す（シンプル版でテスト）
window.reservationData = {
    timeSlots: <?= json_encode($time_slots) ?>,
    workTypes: <?= json_encode($work_types) ?>,
    currentReservation: null
};
</script>
<?= $this->endSection() ?>