<?php

/**
 * User Preference Helper (最小限実装)
 * ユーザー個人設定関連のシンプルなヘルパー関数
 */

if (!function_exists('user_preference')) {
    /**
     * ユーザーの個人設定値を取得します。
     *
     * @param string $key 設定キー
     * @param mixed $default デフォルト値
     * @return mixed 設定値
     */
    function user_preference(string $key, $default = null)
    {
        $userId = auth()->id();
        if ($userId === null) {
            return $default;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('user_preferences');
        $result = $builder->where('user_id', $userId)
                         ->where('preference_key', $key)
                         ->get()
                         ->getRow();

        if ($result && $result->preference_value !== null) {
            return $result->preference_value;
        }

        return $default;
    }
}

if (!function_exists('set_user_preference')) {
    /**
     * ユーザーの個人設定値を保存します。
     *
     * @param string $key 設定キー
     * @param mixed $value 設定値
     * @return bool 成功した場合true
     */
    function set_user_preference(string $key, $value): bool
    {
        $userId = auth()->id();
        if ($userId === null) {
            return false;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('user_preferences');
        
        // 既存データをチェック
        $existing = $builder->where('user_id', $userId)
                          ->where('preference_key', $key)
                          ->get()
                          ->getRow();

        $data = [
            'user_id' => $userId,
            'preference_key' => $key,
            'preference_value' => $value,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            // 更新
            return $builder->where('user_id', $userId)
                          ->where('preference_key', $key)
                          ->update($data);
        } else {
            // 新規作成
            $data['created_at'] = date('Y-m-d H:i:s');
            return $builder->insert($data);
        }
    }
}