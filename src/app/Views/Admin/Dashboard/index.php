<?= $this->extend('Layouts/admin_layout') // 管理者向け共通レイアウトを継承 ?>

<?= $this->section('title') // レイアウトの <title> タグに値を設定 ?>
    <?= esc($page_title ?? 'ダッシュボード') ?>
<?= $this->endSection() ?>

<?php // ページヘッダーセクション ?>
<?= $this->section('page_header_content') ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-house-fill"></i> <?php // アイコンは適宜調整 ?>
            <?= esc($h1_title) ?>
        </h1>
        <?php // datetime-info は header-info に変更し、スタイルは _admin-layout.scss で定義 ?>
        <div class="header-info">
            <?= date('Y年n月j日 (D)') // PHPで動的に日付を表示する例 ?>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('content') // レイアウトのメインコンテンツ部分 ?>
    <?php // dashboard.html の .dashboard-content 構造をここから開始 ?>
    <div class="page-content">
        <!-- Today's Schedule -->
        <div class="today-schedule">
            <div class="section-header">
                <div class="d-flex align-items-center">
                    <h2 class="section-title">
                        <i class="bi bi-calendar-day"></i>
                        本日の予定
                    </h2>
                    <span class="section-date">2025年2月7日</span>
                </div>
                <a href="#" class="btn-entry">
                    <i class="bi bi-printer me-2"></i>
                    入庫予定表印刷
                </a>
            </div>
            
            <div class="schedule-grid">
                <!-- Schedule Card 1 -->
                <div class="schedule-card">
                    <div class="schedule-time">
                        <i class="bi bi-clock"></i>
                        09:30
                    </div>
                    <div class="customer-name">
                        <i class="bi bi-person-circle"></i>
                        鈴木　一郎様
                    </div>
                    <div class="work-type-badge badge-clear">Clear車検（本店）</div>
                    <div class="schedule-details">
                        <div><strong>連絡先：</strong>090-XXXX-XXXX</div>
                        <div><strong>車種／車番：</strong>スカイライン 帯広330る587</div>
                        <div><strong>車検満了：</strong>2025年2月20日</div>
                    </div>
                </div>

                <!-- Schedule Card 2 -->
                <div class="schedule-card">
                    <div class="schedule-time">
                        <i class="bi bi-clock"></i>
                        13:45
                    </div>
                    <div class="customer-name">
                        <i class="bi bi-person-circle"></i>
                        田中　花子様
                    </div>
                    <div class="work-type-badge badge-clear">Clear車検（本店）</div>
                    <div class="schedule-details">
                        <div><strong>連絡先：</strong>090-YYYY-YYYY</div>
                        <div><strong>車種／車番：</strong>フィット 帯広550お1234</div>
                        <div><strong>車検満了：</strong>2025年2月20日</div>
                    </div>
                </div>

                <!-- Schedule Card 3 -->
                <div class="schedule-card">
                    <div class="schedule-time">
                        <i class="bi bi-clock"></i>
                        14:00
                    </div>
                    <div class="customer-name">
                        <i class="bi bi-person-circle"></i>
                        佐藤　太郎様
                    </div>
                    <div class="work-type-badge badge-clear">Clear車検（本店）</div>
                    <div class="schedule-details">
                        <div><strong>連絡先：</strong>090-ZZZZ-ZZZZ</div>
                        <div><strong>車種／車番：</strong>エスクァイア 帯広330る505</div>
                        <div><strong>車検満了：</strong>2025年3月12日</div>
                    </div>
                </div>

                <!-- Schedule Card 4 -->
                <div class="schedule-card">
                    <div class="schedule-time">
                        <i class="bi bi-clock"></i>
                        16:35
                    </div>
                    <div class="customer-name">
                        <i class="bi bi-person-circle"></i>
                        山田　美香様
                    </div>
                    <div class="work-type-badge badge-clear">Clear車検（本店）</div>
                    <div class="schedule-details">
                        <div><strong>連絡先：</strong>090-AAAA-AAAA</div>
                        <div><strong>車種／車番：</strong>ノート 帯広550お5678</div>
                        <div><strong>車検満了：</strong>2025年2月28日</div>
                    </div>
                </div>
            </div>
    
            <div class="more-link">
                <a href="#">
                    <i class="bi bi-arrow-right me-2"></i>
                    もっと見る
                </a>
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="calendar-section">
            <div class="calendar-header">
                <div>
                    <h2 class="section-title">
                        <i class="bi bi-calendar3"></i>
                        予約カレンダー
                    </h2>
                    <div class="calendar-stats">合計件数　15件／Clear車検　10件／一般整備　5件／その他　3件</div>
                </div>
                <div>
                    <label for="shopSelect" class="form-label me-2" style="color: #1f2937; font-weight: 600;">作業店舗</label>
                    <select id="shopSelect" class="shop-select">
                        <option selected>本社（車検・整備工場）</option>
                        <option>Clear車検</option>
                        <option>モーターショップカミシマ</option>
                    </select>
                </div>
            </div>

            <div class="calendar-container">
                <div class="calendar-nav">
                    <button class="calendar-nav-btn">
                        <i class="bi bi-chevron-left me-1"></i>
                        前月
                    </button>
                    <div class="calendar-month">2025年2月</div>
                    <button class="calendar-nav-btn">
                        次月
                        <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </div>

                <div class="calendar-scroll-container">
                    <div class="calendar-table-wrapper">
                        <table class="calendar-table">
                            <thead>
                                <tr class="calendar-header-row">
                                    <th class="calendar-header-cell sunday">日</th>
                                    <th class="calendar-header-cell">月</th>
                                    <th class="calendar-header-cell">火</th>
                                    <th class="calendar-header-cell">水</th>
                                    <th class="calendar-header-cell">木</th>
                                    <th class="calendar-header-cell">金</th>
                                    <th class="calendar-header-cell saturday">土</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="calendar-cell"><div class="calendar-date"><span class="date-number prev-month"></span></div></td>
                                    <td class="calendar-cell"><div class="calendar-date"><span class="date-number prev-month"></span></div></td>
                                    <td class="calendar-cell"><div class="calendar-date"><span class="date-number prev-month"></span></div></td>
                                    <td class="calendar-cell"><div class="calendar-date"><span class="date-number prev-month"></span></div></td>
                                    <td class="calendar-cell"><div class="calendar-date"><span class="date-number prev-month"></span></div></td>
                                    <td class="calendar-cell"><div class="calendar-date"><span class="date-number prev-month"></span></div></td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number saturday">1</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">09:30 鈴木</a>
                                            <a href="#" class="appointment-time appointment-booked">16:30 田中</a>
                                        </div>
                                        <div class="holiday-label">車検 2件</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number sunday">2</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">09:30 佐藤</a>
                                        </div>
                                        <div class="holiday-label">車検 1件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">3</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">09:30 山田</a>
                                        </div>
                                        <div class="holiday-label">車検 1件</div>
                                    </td>
                                    <td class="calendar-cell holiday">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">4</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <span class="appointment-new">定休日</span>
                                        </div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">5</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">09:30 高橋</a>
                                        </div>
                                        <div class="holiday-label">車検 1件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">6</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">09:30 中村</a>
                                        </div>
                                        <div class="holiday-label">車検 1件</div>
                                    </td>
                                    <td class="calendar-cell today">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">7</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">09:30 鈴木</a>
                                            <a href="#" class="appointment-time appointment-booked">13:45 田中</a>
                                            <a href="#" class="appointment-time appointment-booked">16:35 山田</a>
                                        </div>
                                        <div class="holiday-label">車検 4件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number saturday">8</span>
                                        </div>
                                        <div class="calendar-appointments">
                                        </div>
                                        <div class="holiday-label">車検 0件</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number sunday">9</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">14:30 渡辺</a>
                                        </div>
                                        <div class="holiday-label">車検 1件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">10</span>
                                        </div>
                                        <div class="calendar-appointments">
                                        </div>
                                        <div class="holiday-label">車検 0件</div>
                                    </td>
                                    <td class="calendar-cell holiday">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">11</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <span class="appointment-new">定休日</span>
                                        </div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">12</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">09:30 小林</a>
                                            <a href="#" class="appointment-time appointment-booked">11:00 松田</a>
                                            <a href="#" class="appointment-time appointment-booked">13:45 橋本</a>
                                            <a href="#" class="appointment-time appointment-booked">15:15 木下</a>
                                            <a href="#" class="appointment-time appointment-booked">16:45 清水</a>
                                        </div>
                                        <div class="holiday-label">車検 5件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">13</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">15:15 加藤</a>
                                        </div>
                                        <div class="holiday-label">車検 1件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">14</span>
                                        </div>
                                        <div class="calendar-appointments">
                                        </div>
                                        <div class="holiday-label">車検 0件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number saturday">15</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">11:00 松本</a>
                                        </div>
                                        <div class="holiday-label">車検 1件</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number sunday">16</span>
                                        </div>
                                        <div class="calendar-appointments">
                                        </div>
                                        <div class="holiday-label">車検 0件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">17</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">13:00 井上</a>
                                        </div>
                                        <div class="holiday-label">車検 1件</div>
                                    </td>
                                    <td class="calendar-cell holiday">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">18</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <span class="appointment-new">定休日</span>
                                        </div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">19</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">16:00 木村</a>
                                        </div>
                                        <div class="holiday-label">車検 1件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">20</span>
                                        </div>
                                        <div class="calendar-appointments">
                                        </div>
                                        <div class="holiday-label">車検 0件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">21</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">14:30 斎藤</a>
                                        </div>
                                        <div class="holiday-label">車検 1件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number saturday">22</span>
                                        </div>
                                        <div class="calendar-appointments">
                                        </div>
                                        <div class="holiday-label">車検 0件</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number sunday">23</span>
                                        </div>
                                        <div class="calendar-appointments">
                                        </div>
                                        <div class="holiday-label">車検 0件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">24</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">09:30 遠藤</a>
                                        </div>
                                        <div class="holiday-label">車検 1件</div>
                                    </td>
                                    <td class="calendar-cell holiday">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">25</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <span class="appointment-new">定休日</span>
                                        </div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">26</span>
                                        </div>
                                        <div class="calendar-appointments">
                                        </div>
                                        <div class="holiday-label">車検 0件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">27</span>
                                        </div>
                                        <div class="calendar-appointments">
                                            <a href="#" class="appointment-time appointment-booked">11:00 藤田</a>
                                        </div>
                                        <div class="holiday-label">車検 1件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <button class="new-reservation-btn">＋新規</button>
                                            <span class="date-number">28</span>
                                        </div>
                                        <div class="calendar-appointments">
                                        </div>
                                        <div class="holiday-label">車検 0件</div>
                                    </td>
                                    <td class="calendar-cell">
                                        <div class="calendar-date">
                                            <span class="date-number prev-month"></span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="calendar-pagination">
                    <div class="pagination-dot active"></div>
                    <div class="pagination-dot"></div>
                    <div class="pagination-dot"></div>
                    <div class="pagination-dot"></div>
                    <div class="pagination-dot"></div>
                </div>
            </div>
        </div> <?php // End of calendar-section ?>
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