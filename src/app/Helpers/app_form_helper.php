<?php

/**
 * App Form Helper
 * Flatpickr用の入力フィールド生成など
 */

if (! function_exists('flatpickr_input')) {
    /**
     * Flatpickrを適用するためのinput要素を生成します。
     *
     * @param string $name inputのname属性
     * @param string $value 初期値
     * @param array  $attributes inputに追加する属性 (class, id, data-* など)
     * @param string $type 'date', 'datetime', 'time', 'month', 'dynamic' (flatpickr-date-dynamic用)
     * @return string HTML文字列
     */
    function flatpickr_input(string $name, string $value = '', array $attributes = [], string $type = 'date'): string
    {
        $default_class = 'form-control'; // Bootstrap等の共通クラス
        $flatpickr_class = '';

        switch ($type) {
            case 'datetime':
                $flatpickr_class = 'flatpickr-datetime';
                break;
            case 'time':
                $flatpickr_class = 'flatpickr-time';
                break;
            case 'month':
                $flatpickr_class = 'flatpickr-month';
                break;
            case 'dynamic': // 動的オプション用
                $flatpickr_class = 'flatpickr-date-dynamic'; // JS側で定義したクラス名に合わせる
                break;
            case 'date':
            default:
                $flatpickr_class = 'flatpickr-date';
                break;
        }

        // 既存のクラス属性とマージ
        if (isset($attributes['class'])) {
            $attributes['class'] = trim($default_class . ' ' . $flatpickr_class . ' ' . $attributes['class']);
        } else {
            $attributes['class'] = trim($default_class . ' ' . $flatpickr_class);
        }
        
        // value属性を設定
        $attributes['value'] = esc($value, 'attr');
        $attributes['name']  = $name;
        if (empty($attributes['id'])) {
            $attributes['id'] = $name; // idがなければnameと同じにする
        }

        $attr_str = '';
        foreach ($attributes as $key => $val) {
            // data-* 属性の値が配列やオブジェクトの場合はJSONエンコードする
            if (strpos($key, 'data-') === 0 && (is_array($val) || is_object($val))) {
                 $attr_str .= ' ' . esc($key, 'attr') . "='" . esc(json_encode($val), 'attr') . "'";
            } else {
                 $attr_str .= ' ' . esc($key, 'attr') . '="' . esc($val, 'attr') . '"';
            }
        }

        return '<input type="text"' . $attr_str . '>';
    }
}

if (! function_exists('format_for_flatpickr_disabled')) {
    /**
     * 予約不可日などの日付配列をFlatpickrのdisableオプション用の形式に変換します。
     * @param array $dates ['YYYY-MM-DD', 'YYYY-MM-DD', ...] 形式の日付配列
     * @return string JSON文字列化された日付配列
     */
    function format_for_flatpickr_disabled(array $dates): string
    {
        // 必要であればここで日付のフォーマット検証などを行う
        return json_encode($dates);
    }
}