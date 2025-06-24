<?= $this->extend('Layouts/admin_layout') // 管理者向け共通レイアウトを継承 ?>

<?php // ページヘッダーセクション ?>
<?= $this->section('title') // レイアウトの <title> タグに値を設定 ?>
    <?= esc($page_title ?? '予約検索／一覧') ?>
<?= $this->endSection() ?>

<?= $this->section('page_header_content') ?>
    <?php // reservations-list.html の .page-header 構造を移植 ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-search"></i> <?php // アイコンは適宜調整 ?>
            <?= esc($h1_title) ?>
        </h1>
    </div>
<?= $this->endSection() ?>

<?= $this->section('content') // レイアウトのメインコンテンツ部分 ?>
    <?php // reservations-list.html の .search-content 構造を移植 ?>
    <div class="page-content">
        <!-- Search Section -->
        <div class="search-section">
            <h2 class="search-title">
                <i class="bi bi-funnel"></i>
                検索条件
            </h2>

            <div class="search-row search-row-main">
                <div class="search-group narrow">
                    <label class="form-label">お名前</label>
                    <input type="text" class="form-control" placeholder="">
                </div>
                <div class="search-group narrow">
                    <label class="form-label">車番</label>
                    <input type="text" class="form-control" placeholder="">
                </div>
                <div class="search-group narrow">
                    <label class="form-label">LINE識別</label>
                    <input type="text" class="form-control" placeholder="">
                </div>
                <div class="search-group medium">
                    <label class="form-label">作業店舗</label>
                    <select class="form-select">
                        <option value="">すべて</option>
                        <option>本社（車検・整備工場）</option>
                        <option>Clear車検</option>
                        <option>モーターショップカミシマ</option>
                    </select>
                </div>
                <div class="search-group wide search-group-date-pc">
                    <label class="form-label">予約希望日</label>
                    <div class="date-range-container">
                        <input type="text" class="form-control flatpickr-date" placeholder="開始日">
                        <span class="date-separator">～</span>
                        <input type="text" class="form-control flatpickr-date" placeholder="終了日">
                    </div>
                </div>
            </div>

            <div class="search-row search-row-date">
                <div class="search-group wide">
                    <label class="form-label">予約希望日</label>
                    <div class="date-range-container">
                        <input type="text" class="form-control flatpickr-date" placeholder="開始日">
                        <span class="date-separator">～</span>
                        <input type="text" class="form-control flatpickr-date" placeholder="終了日">
                    </div>
                </div>
            </div>

            <div class="search-row">
                <div class="search-group work-types-group">
                    <label class="form-label">作業種別</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" class="form-check-input" id="clear" checked>
                            <label for="clear" class="form-check-label">Clear車検</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" class="form-check-input" id="inspection" checked>
                            <label for="inspection" class="form-check-label">定期点検</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" class="form-check-input" id="generalShaken" checked>
                            <label for="generalShaken" class="form-check-label">一般車検</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" class="form-check-input" id="general" checked>
                            <label for="general" class="form-check-label">一般整備</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" class="form-check-input" id="leaseSchedule" checked>
                            <label for="leaseSchedule" class="form-check-label">リーススケジュール点検</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" class="form-check-input" id="leaseStatutory" checked>
                            <label for="leaseStatutory" class="form-check-label">リース法定点検</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" class="form-check-input" id="leaseShaken" checked>
                            <label for="leaseShaken" class="form-check-label">リース車検</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" class="form-check-input" id="leaseMaintenance" checked>
                            <label for="leaseMaintenance" class="form-check-label">リース整備</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" class="form-check-input" id="bodywork" checked>
                            <label for="bodywork" class="form-check-label">板金</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" class="form-check-input" id="adjustmentClear" checked>
                            <label for="adjustmentClear" class="form-check-label">調整枠 (Clear車検)</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" class="form-check-input" id="other" checked>
                            <label for="other" class="form-check-label">その他</label>
                        </div>
                    </div>
                </div>

                <!-- Quick Search -->
                <div class="quick-search">
                    <label class="form-label">クイック検索</label>
                    <div class="quick-buttons">
                        <button type="button" class="btn btn-quick-small">本日の作業</button>
                        <button type="button" class="btn btn-quick-small">未完了</button>
                        <button type="button" class="btn btn-quick-small">今月整備完了予定</button>
                        <button type="button" class="btn btn-quick-small">本社作業</button>
                        <button type="button" class="btn btn-quick-small">12条店作業</button>
                    </div>
                </div>

                <!-- Search Actions -->
                <div class="search-actions-mobile">
                    <button type="button" class="btn-search">
                        <i class="bi bi-search me-2"></i>検索
                    </button>
                    <a href="#" class="btn-clear">
                        <i class="bi bi-arrow-clockwise me-2"></i>条件クリア
                    </a>
                </div>
            </div>

        </div>

        <!-- Results Section -->
        <div class="results-section">
            <div class="results-header">
                <div class="results-count">検索結果：23件</div>
                <a href="<?= route_to('admin.reservations.new') ?>" class="btn-create-new">
                    <i class="bi bi-plus-circle me-2"></i>新規予約作成
                </a>
            </div>

            <!-- Results Table -->
            <div class="table-container"> <?php // table-container は _tables.scss で定義 ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>予約番号</th>
                            <th>予約状況</th>
                            <th>予約希望日</th>
                            <th>お名前</th>
                            <th>車種</th>
                            <th>車番</th>
                            <th>作業種別</th>
                            <th>作業店舗</th>
                            <th>処理</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>00010375</td>
                            <td><span class="status-badge status-pending">未確定</span></td>
                            <td>2025/02/05 09:30</td> <?php // status-badge は _badges.scss で定義 ?>
                            <td>鈴木　一郎</td>
                            <td>スカイライン</td>
                            <td>帯広330る583</td>
                            <td><span class="work-type-tag work-clear">Clear車検</span></td>
                            <td>本社</td>
                            <td>
                                <a href="#" class="btn-action btn-edit">修正</a>
                                <a href="#" class="btn-action btn-complete">完了</a>
                            </td>
                        </tr>
                        <tr>
                            <td>00010376</td>
                            <td><span class="status-badge status-confirmed">予約確定</span></td>
                            <td>2025/02/06 14:30</td> <?php // status-badge は _badges.scss で定義 ?>
                            <td>吉田　宏</td>
                            <td>デミオ</td>
                            <td>帯広563ね1120</td>
                            <td><span class="work-type-tag work-clear">Clear車検</span></td>
                            <td>本社</td>
                            <td>
                                <a href="#" class="btn-action btn-edit">修正</a>
                                <a href="#" class="btn-action btn-complete">完了</a>
                            </td>
                        </tr>
                        <tr>
                            <td>00010380</td>
                            <td><span class="status-badge status-confirmed">予約確定</span></td>
                            <td>2025/02/06 16:45</td> <?php // status-badge は _badges.scss で定義 ?>
                            <td>田中　直人</td>
                            <td>マークX</td>
                            <td>帯広331な8721</td>
                            <td><span class="work-type-tag work-general">一般整備</span></td>
                            <td>12条店</td>
                            <td>
                                <a href="#" class="btn-action btn-edit">修正</a>
                                <a href="#" class="btn-action btn-complete">完了</a>
                            </td>
                        </tr>
                        <tr>
                            <td>00010382</td>
                            <td><span class="status-badge status-confirmed">予約確定</span></td>
                            <td>2025/02/06 14:30</td> <?php // status-badge は _badges.scss で定義 ?>
                            <td>山本　国広</td>
                            <td>CR-V</td>
                            <td>帯広332ね1023</td>
                            <td><span class="work-type-tag work-maintenance">預かり車検</span></td>
                            <td>本社</td>
                            <td>
                                <a href="#" class="btn-action btn-edit">修正</a>
                                <a href="#" class="btn-action btn-complete">完了</a>
                            </td>
                        </tr>
                        <tr>
                            <td>00010423</td>
                            <td><span class="status-badge status-confirmed">予約確定</span></td>
                            <td>2025/02/15 16:45</td> <?php // status-badge は _badges.scss で定義 ?>
                            <td>田中　直人</td>
                            <td>マークX</td>
                            <td>帯広331な8721</td>
                            <td><span class="work-type-tag work-general">一般整備</span></td>
                            <td>12条店</td>
                            <td>
                                <a href="#" class="btn-action btn-edit">修正</a>
                                <a href="#" class="btn-action btn-complete">完了</a>
                            </td>
                        </tr>
                        <tr>
                            <td>00010450</td>
                            <td><span class="status-badge status-pending">未確定</span></td>
                            <td>2025/02/16 14:30</td> <?php // status-badge は _badges.scss で定義 ?>
                            <td>宮野　芳雄</td>
                            <td>フィット</td>
                            <td>帯広550お3271</td>
                            <td><span class="work-type-tag work-maintenance">預かり車検</span></td>
                            <td>本社</td>
                            <td>
                                <a href="#" class="btn-action btn-edit">修正</a>
                                <a href="#" class="btn-action btn-complete">完了</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-container"> <?php // pagination-container は _pagination.scss で定義 ?>
                <div class="pagination-dots">
                    <div class="pagination-dot active"></div>
                    <div class="pagination-dot"></div>
                    <div class="pagination-dot"></div>
                    <div class="pagination-dot"></div>
                    <div class="pagination-dot"></div>
                </div>
            </div>

            <!-- Export Actions -->
            <div class="export-actions"> <?php // export-actions は _index.scss に残し、btn-export は _buttons.scss で定義 ?>
                <a href="#" class="btn-export">
                    <i class="bi bi-clipboard me-2"></i>クリップボード
                </a>
                <a href="#" class="btn-export">
                    <i class="bi bi-download me-2"></i>CSVダウンロード
                </a>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?php // このページ固有の <head> 内アセット (例: CSS) を追加する場合は以下を使用 ?>
<?php /* echo $this->section('page_specific_before_head_end') ?>
    <-- <link rel="stylesheet" href="path/to/specific.css"> -->
<?= $this->endSection() */ ?>

<?php // このページ固有の </body> 直前アセット (例: JS) を追加する場合は以下を使用 ?>
<?php /* echo $this->section('page_specific_before_body_end') ?>
    <-- <script src="path/to/specific.js"></script> -->
<?= $this->endSection() */ ?>
