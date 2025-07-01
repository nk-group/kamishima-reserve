<?php // src/app/Views/Partials/_user_preferences_modal.php ?>

<!-- 個人設定モーダル -->
<div class="modal fade" id="userPreferencesModal" tabindex="-1" aria-labelledby="userPreferencesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userPreferencesModalLabel">
                    <i class="bi bi-gear-fill me-2"></i>個人設定
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
            </div>
            <div class="modal-body">
                <form id="userPreferencesForm">
                    <?= csrf_field() ?>
                    
                    <!-- デフォルト店舗選択 -->
                    <div class="mb-3">
                        <label for="defaultShopId" class="form-label">デフォルト店舗</label>
                        <select class="form-select" id="defaultShopId" name="default_shop_id">
                            <option value="">-- 店舗を選択してください --</option>
                            <!-- 店舗オプションはJavaScriptで動的に追加 -->
                        </select>
                        <div class="form-text">新規予約作成時に自動選択される店舗です。</div>
                    </div>

                    <!-- ページネーション件数選択 -->
                    <div class="mb-3">
                        <label for="paginationPerPage" class="form-label">一覧表示件数</label>
                        <select class="form-select" id="paginationPerPage" name="pagination_per_page">
                            <option value="10">10件</option>
                            <option value="20">20件</option>
                            <option value="50">50件</option>
                            <option value="100">100件</option>
                        </select>
                        <div class="form-text">一覧ページで1ページあたりに表示する件数です。</div>
                    </div>
                </form>

                <!-- メッセージ表示エリア -->
                <div id="userPreferencesMessage" class="alert alert-dismissible fade" role="alert" style="display: none;">
                    <span id="userPreferencesMessageText"></span>
                    <button type="button" class="btn-close" aria-label="閉じる" onclick="hideUserPreferencesMessage()"></button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>キャンセル
                </button>
                <button type="button" class="btn btn-primary" id="saveUserPreferencesBtn">
                    <i class="bi bi-check-circle me-2"></i>保存
                </button>
            </div>
        </div>
    </div>
</div>