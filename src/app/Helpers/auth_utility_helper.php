<?php

/**
 * Auth Utility Helper
 * Shieldの認証機能を補完するカスタムヘルパー関数
 */

use CodeIgniter\Shield\Entities\User; // Userエンティティをインポート

if (! function_exists('current_user_entity')) {
    /**
     * 現在ログインしているユーザーのエンティティを取得します。
     * ログインしていない場合は null を返します。
     * auth()->user() の型ヒント付きラッパーとして機能します。
     *
     * @return User|null
     */
    function current_user_entity(): ?User
    {
        $authenticator = service('auth')->getAuthenticator();
        if ($authenticator->loggedIn()) {
            return $authenticator->getUser();
        }
        return null;
    }
}

if (! function_exists('is_admin')) {
    /**
     * 現在のユーザーが 'admin' グループに属しているかを確認します。
     * @return bool
     */
    function is_admin(): bool
    {
        $user = current_user_entity();
        return $user && $user->inGroup('admin');
    }
}

if (! function_exists('is_staff')) {
    /**
     * 現在のユーザーが 'staff' グループに属しているかを確認します。
     * (adminもstaff権限を持つ場合があるため、adminも含むか別途検討)
     * @return bool
     */
    function is_staff(): bool
    {
        $user = current_user_entity();
        // adminはstaffの全ての権限を持つという前提なら以下のようにするか、
        // もしくは純粋にstaffグループに属しているかだけを見るか選択
        return $user && ($user->inGroup('staff') || $user->inGroup('admin'));
        // return $user && $user->inGroup('staff'); // 純粋にstaffグループの場合
    }
}

if (! function_exists('user_can')) {
    /**
     * 現在のユーザーが指定されたパーミッションを持っているか確認します。
     * auth()->user()->can() のショートカット。
     *
     * @param string $permission パーミッションコード (例: 'users.create')
     * @return bool
     */
    function user_can(string $permission): bool
    {
        $user = current_user_entity();
        return $user && $user->can($permission);
    }
}

