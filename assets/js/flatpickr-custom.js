import flatpickr from "flatpickr";
import { Japanese } from "flatpickr/dist/l10n/ja.js"; // 日本語ロケール
// 必要であれば他のプラグインやテーマもインポート
// import 'flatpickr/dist/themes/material_blue.css'; // 例: テーマCSS

/**
 * Flatpickrの共通初期化処理
 */
function initializeFlatpickr() {
    // 日付選択用 (クラス名: .flatpickr-date)
    flatpickr(".flatpickr-date", {
        locale: Japanese,
        dateFormat: "Y-m-d", // サーバーサイドで扱いやすい形式
        altInput: true,       // 代替表示用のinputを生成
        altFormat: "Y年m月d日", // ユーザーに見やすい形式
        allowInput: true,     // 手入力も許可 (バリデーションは別途必要)
        // disableMobile: true, // モバイルでネイティブピッカーを使わせない場合
        // その他の共通オプション
    });

    // 日時選択用 (クラス名: .flatpickr-datetime)
    flatpickr(".flatpickr-datetime", {
        locale: Japanese,
        enableTime: true,
        dateFormat: "Y-m-d H:i", // サーバーサイドで扱いやすい形式 (秒が必要なら H:i:S)
        altInput: true,
        altFormat: "Y年m月d日 H時i分",
        allowInput: true,
        time_24hr: true,
        minuteIncrement: 15, // 15分刻みなど
        // その他の共通オプション
    });

    // 時刻選択のみ (クラス名: .flatpickr-time)
    flatpickr(".flatpickr-time", {
        locale: Japanese,
        enableTime: true,
        noCalendar: true, // カレンダー非表示
        dateFormat: "H:i",
        altInput: true,
        altFormat: "H時i分",
        allowInput: true,
        time_24hr: true,
        minuteIncrement: 15,
    });

    // 予約不可日などを動的に設定する例
    // HTML側の input要素に data-disabled-dates='["2025-05-20", "2025-05-25"]' のようにJSON文字列を埋め込む
    document.querySelectorAll('.flatpickr-date-dynamic').forEach(function(element) {
        let options = {
            locale: Japanese,
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "Y年m月d日",
            allowInput: true,
        };
        if (element.dataset.disabledDates) {
            try {
                options.disable = JSON.parse(element.dataset.disabledDates);
            } catch (e) {
                console.error("Failed to parse disabled dates for Flatpickr:", e);
            }
        }
        if (element.dataset.minDate) {
            options.minDate = element.dataset.minDate;
        }
        // 他にもdata属性で渡したいオプションがあれば追加
        flatpickr(element, options);
    });
}

// DOMContentLoaded後、またはViteなどのモジュールシステムで適切に呼び出す
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeFlatpickr);
} else {
    initializeFlatpickr();
}

// もし特定の関数をグローバルに公開したい場合は window に代入するか、exportする
// export { initializeFlatpickr };