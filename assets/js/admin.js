// kamishima-reserve/assets/js/admin.js
import 'bootstrap';
// SCSS/CSSのインポートはvite.config.jsのエントリーポイントで行うため、ここでは不要

import { initCommon } from './common.js';

// Flatpickrの初期化スクリプトをインポートして実行
import './admin/plugins/flatpickr-init.js';

// 共通UIインタラクション（ツールチップ、フォーム制御など）をインポート
import { initAdminUIInteractions } from './admin/ui-interactions.js';

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

    // --- ページ固有のスクリプトを動的インポートで読み込む ---
    // 例: 管理者ダッシュボード
    // HTMLの<body>タグに <body id="page-admin-dashboard"> のようにIDを付与することを想定
    const bodyId = document.body.id;

    switch (bodyId) {
        case 'page-admin-dashboard':
            try {
                const { initDashboard } = await import('./admin/pages/dashboard.js');
                initDashboard();
            } catch (e) {
                console.error('Failed to load dashboard scripts:', e);
            }
            break;

        case 'page-admin-reservations-index':
            try {
                // このファイルはイベントリスナーを直接セットアップするため、インポートするだけで機能します。
                await import('./admin/pages/reservations-list.js');
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

        // case 'page-admin-reservations-detail':
        //     // 予約詳細ページ用のスクリプトもここに追加できます。
        //     break;
    }
});