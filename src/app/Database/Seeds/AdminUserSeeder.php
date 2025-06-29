<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $users = auth()->getProvider();
        
        // 管理者ユーザーを作成
        $user = new User([
            'username' => 'admin',
            'email'    => 'admin@kamishima.co.jp',
            'password' => 'Password_',
        ]);
        
        $users->save($user);
        
        // 管理者グループに追加
        $user = $users->findById($users->getInsertID());
        $user->addGroup('admin');
    }
}