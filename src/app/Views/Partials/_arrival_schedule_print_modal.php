<?php // src/app/Views/Partials/_arrival_schedule_print_modal.php ?>

<!-- 入庫予定表印刷モーダル -->
<div class="modal fade" id="arrivalSchedulePrintModal" tabindex="-1" aria-labelledby="arrivalSchedulePrintModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="arrivalSchedulePrintModalLabel">
                    <i class="bi bi-printer-fill me-2"></i>入庫予定表印刷
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
            </div>
            <div class="modal-body">
                <form id="arrivalSchedulePrintForm">
                    <?= csrf_field() ?>
                    
                    <!-- 日付選択 -->
                    <div class="mb-3">
                        <label for="printDate" class="form-label">印刷対象日</label>
                        <input type="date" class="form-select" id="printDate" name="print_date" 
                               value="<?= date('Y-m-d') ?>" required>
                        <div class="form-text">入庫予定表を印刷する日付を選択してください。</div>
                    </div>

                    <!-- 日付範囲選択（将来的な拡張用） -->
                    <div class="mb-3" style="display: none;" id="dateRangeSection">
                        <label class="form-label">印刷範囲</label>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="printDateFrom" class="form-label">開始日</label>
                                <input type="date" class="form-select" id="printDateFrom" name="print_date_from">
                            </div>
                            <div class="col-md-6">
                                <label for="printDateTo" class="form-label">終了日</label>
                                <input type="date" class="form-select" id="printDateTo" name="print_date_to">
                            </div>
                        </div>
                    </div>
                </form>

                <!-- メッセージ表示エリア -->
                <div id="arrivalSchedulePrintMessage" class="alert alert-dismissible fade" role="alert" style="display: none;">
                    <span id="arrivalSchedulePrintMessageText"></span>
                    <button type="button" class="btn-close" aria-label="閉じる" onclick="hideArrivalSchedulePrintMessage()"></button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>キャンセル
                </button>
                <button type="button" class="btn btn-primary" id="executeArrivalSchedulePrintBtn">
                    <i class="bi bi-printer-fill me-2"></i>印刷
                </button>
            </div>
        </div>
    </div>
</div>