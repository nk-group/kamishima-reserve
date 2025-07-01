// kamishima-reserve/assets/js/admin.js
import 'bootstrap';
// SCSS/CSSのインポートはvite.config.jsのエントリーポイントで行うため、ここでは不要

import { initCommon } from './common.js';

// Flatpickrの初期化スクリプトをインポートして実行
import './admin/plugins/flatpickr-init.js';

// 共通UIインタラクション（ツールチップ、フォーム制御など）をインポート
import { initAdminUIInteractions } from './admin/common/ui-common.js';

// 個人設定機能をインポート
import { initUserPreferences } from './admin/common/user-preferences.js';

// 共通フォームバリデーションスクリプトをインポート
import { initFormValidation } from './admin/utils/form-validation.js';

document.addEventListener('DOMContentLoaded', async () => {
    console.log('Vite app loaded and DOM ready!');
    // 共通UIインタラクションを初期化
    // initAdminUIInteractions() はDOM要素に依存するため、DOMContentLoaded内で呼び出す
    // Flatpickrの初期化はadmin/plugins/flatpickr-init.js内でDOMContentLoadedを待機しているため、ここでは不要

    // 共通処理の初期化
    initCommon();
    initAdminUIInteractions(); // 共通UI初期化関数を呼び出し
    initFormValidation(); // 共通フォームバリデーションを初期化

    // 個人設定機能の初期化
    initUserPreferences();

    // --- ページ固有のスクリプトを動的インポートで読み込む ---
    // 例: 管理者ダッシュボード
    // HTMLの<body>タグに <body id="page-admin-dashboard"> のようにIDを付与することを想定
    const bodyId = document.body.id;

    switch (bodyId) {
        case 'page-admin-dashboard':
            try {
                const { initDashboard } = await import('./admin/pages/dashboard/index.js');
                initDashboard();
            } catch (e) {
                console.error('Failed to load dashboard scripts:', e);
            }
            break;

        // === 予約管理（統一されたフォルダ分割パターン） ===
        case 'page-admin-reservations-index':
            try {
                const { initReservationsIndex } = await import('./admin/pages/reservations/index.js');
                initReservationsIndex();
            } catch (e) {
                console.error('Failed to load reservation list scripts:', e);
            }
            break;

        case 'page-admin-reservations-new':
            try {
                const { initNewReservation } = await import('./admin/pages/reservations/new.js');
                initNewReservation();
            } catch (e) {
                console.error('Failed to load reservation new scripts:', e);
            }
            break;

        case 'page-admin-reservations-detail':
            try {
                const { initEditReservation } = await import('./admin/pages/reservations/edit.js');
                initEditReservation();
            } catch (e) {
                console.error('Failed to load reservation edit scripts:', e);
            }
            break;

        // === 定休日マスタ（フォルダ分割パターン） ===
        case 'page-admin-shop-closing-days-index':
            try {
                const { initShopClosingDaysIndex } = await import('./admin/pages/shop-closing-days/index.js');
                initShopClosingDaysIndex();
            } catch (e) {
                console.error('Failed to load shop closing days index scripts:', e);
            }
            break;

        case 'page-admin-shop-closing-days-form':
            try {
                const { initShopClosingDaysForm } = await import('./admin/pages/shop-closing-days/form.js');
                initShopClosingDaysForm();
            } catch (e) {
                console.error('Failed to load shop closing days form scripts:', e);
            }
            break;

        case 'page-admin-shop-closing-days-batch':
            try {
                const { initShopClosingDaysBatch } = await import('./admin/pages/shop-closing-days/batch.js');
                initShopClosingDaysBatch();
            } catch (e) {
                console.error('Failed to load shop closing days batch scripts:', e);
            }
            break;

        // === リマインドメール管理 ===
        case 'page-admin-reminders-index':
            try {
                const { initRemindersIndex } = await import('./admin/pages/reminders/index.js');
                initRemindersIndex();
            } catch (e) {
                console.error('Failed to load reminders index scripts:', e);
            }
            break;

        // case 'page-admin-other-feature':
        //     // 他の機能も同様にフォルダ分割パターンで追加可能
        //     break;
    }
});