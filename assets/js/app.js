// kamishima-reserve/assets/js/app.js
import 'bootstrap';
import '../css/app.scss';
import 'flatpickr/dist/flatpickr.min.css'; // FlatpickrのCSSはグローバルに読み込む

import { initCommon } from './common.js';

document.addEventListener('DOMContentLoaded', async () => {
    console.log('Vite app loaded and DOM ready!');

    // 共通処理の初期化
    initCommon();

    // --- ページ固有のスクリプトを動的インポートで読み込む ---
    // 例: 管理者ダッシュボード
    // HTMLの<body>タグに <body id="page-admin-dashboard"> のようにIDを付与することを想定
    if (document.body.id === 'page-admin-dashboard') {
        try {
            const { initDashboard } = await import('./pages/admin/dashboard.js');
            initDashboard();
        } catch (e) {
            console.error('Failed to load dashboard scripts:', e);
        }
    }

    // 他のページ固有スクリプトも同様に追加
    // if (document.body.id === 'page-admin-reservations-form') { ... }
});