import flatpickr from "flatpickr";
import 'flatpickr/dist/flatpickr.min.css'; // FlatpickrのデフォルトCSS
import { Japanese } from "flatpickr/dist/l10n/ja.js";
import monthSelectPlugin from 'flatpickr/dist/plugins/monthSelect/index.js'; // flatpickr本体からインポート
import 'flatpickr/dist/plugins/monthSelect/style.css'; // flatpickr本体からインポート

/**
 * サイト全体で利用される様々な種類のFlatpickrインスタンスを初期化します。
 * このスクリプトは、メインのアプリケーションエントリーポイントで一度だけインポートされるべきです。
 */
function initializeAllFlatpickrInstances() {
    // 1. 標準的な日付ピッカー
    // class="flatpickr-date" を持つすべてのinput要素に適用
    flatpickr(".flatpickr-date", {
        locale: Japanese,
        dateFormat: "Y-m-d",
        allowInput: true,
    });

    // 2. 日時選択ピッカー
    // class="flatpickr-datetime" を持つすべてのinput要素に適用
    flatpickr(".flatpickr-datetime", {
        locale: Japanese,
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        minuteIncrement: 15,
    });

    // 3. 月選択ピッカー
    // class="flatpickr-month" を持つすべてのinput要素に適用
    flatpickr(".flatpickr-month", {
        locale: Japanese,
        plugins: [
            new monthSelectPlugin({
                shorthand: true,    // 例: "1月"
                dateFormat: "Y-m",  // サーバー送信用
                altFormat: "Y年n月", // ユーザー表示用
            })
        ]
    });
}

// DOMが完全に読み込まれた後にスクリプトが実行されることを保証します。
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAllFlatpickrInstances);
} else {
    initializeAllFlatpickrInstances();
}