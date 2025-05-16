// kamishima-reserve/assets/js/components/flatpickr-init.js

// flatpickrライブラリ本体とCSSをインポートします。
// 事前に npm install flatpickr --save-dev (または --save) でインストールが必要です。
import flatpickr from "flatpickr";
import 'flatpickr/dist/flatpickr.min.css'; // FlatpickrのデフォルトCSS

// 日本語化のためのロケールファイルをインポート (オプション)
import { Japanese } from "flatpickr/dist/l10n/ja.js";

/**
 * Flatpickrを初期化する関数。
 * HTMLドキュメントが読み込まれた後に実行されます。
 */
function initializeFlatpickr() {
    // --- 日付選択の初期化例 ---
    // HTML内で class="flatpickr-date" を持つ全てのinput要素にFlatpickrを適用します。
    const dateElements = document.querySelectorAll('.flatpickr-date');
    if (dateElements.length > 0) {
        flatpickr(dateElements, { // document.querySelectorAllで取得したNodeListを直接渡せます
            locale: Japanese,      // 日本語化
            dateFormat: "Y-m-d",   // 日付の表示フォーマット (例: 2025-05-08)
            // allowInput: true,   // キーボードからの直接入力を許可する場合
            // disableMobile: true, // モバイル端末でネイティブの日付ピッカーを使用させない場合
            // 他にも多くのオプションがありますので、公式ドキュメントをご参照ください。
        });
        console.log('Flatpickr initialized for .flatpickr-date elements.');
    }

    // --- 日時選択の初期化例 ---
    // HTML内で class="flatpickr-datetime" を持つ全てのinput要素にFlatpickrを適用します。
    const dateTimeElements = document.querySelectorAll('.flatpickr-datetime');
    if (dateTimeElements.length > 0) {
        flatpickr(dateTimeElements, {
            locale: Japanese,
            enableTime: true,                // 時間選択を有効にする
            dateFormat: "Y-m-d H:i",       // 日時の表示フォーマット (例: 2025-05-08 14:30)
            time_24hr: true,               // 24時間表示
            // minuteIncrement: 15,        // 分の刻み幅 (例: 15分ごと)
        });
        console.log('Flatpickr initialized for .flatpickr-datetime elements.');
    }

    // --- 他の用途のFlatpickr初期化が必要な場合は、ここに追加してください ---
    // 例: 期間指定カレンダー (rangePluginを使用)
    // const rangeElements = document.querySelectorAll('.flatpickr-range');
    // if (rangeElements.length > 0) {
    //     flatpickr(rangeElements, {
    //         mode: "range",
    //         locale: Japanese,
    //         dateFormat: "Y-m-d",
    //     });
    // }

    // console.log('All Flatpickr initializations attempted.');
}

// HTMLドキュメントの読み込みが完了した時点で初期化関数を実行します。
if (document.readyState === 'loading') {
    // DOMContentLoadedがまだ発火していない場合
    document.addEventListener('DOMContentLoaded', initializeFlatpickr);
} else {
    // DOMContentLoadedが既に発火済みの場合 (非同期でJSが読み込まれた場合など)
    initializeFlatpickr();
}

// もし、特定の要素に対して動的にFlatpickrを適用したい場合は、
// このファイルから関数をエクスポートして、他のJSファイルから呼び出すことも可能です。
// export { initializeFlatpickr }; // 例