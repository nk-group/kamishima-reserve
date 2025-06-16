<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController; // 正しい名前空間を指定

class DashboardController extends BaseController
{
    public function index(): string
    {
      
        $data = [
            'page_title' => 'ダッシュボード | 車検予約管理システム',
            'body_id'    => 'page-admin-dashboard',
        ];
        // BaseControllerのrenderメソッドを使用してビューを表示
        return $this->render('Admin/dashboard', $data); // app/Views/Admin/Dashboard.php を指す
    }
}