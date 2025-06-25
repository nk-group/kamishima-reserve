<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController; // 正しい名前空間を指定

class DashboardController extends BaseController
{
    public function index(): string
    {
      
        $data = [
            'page_title' => 'ダッシュボード',
            'h1_title' => 'ダッシュボード',
            'body_id' => 'page-admin-dashboard',
        ];
        // BaseControllerのrenderメソッドを使用してビューを表示 (新しいパスを指定)
        return $this->render('Admin/Dashboard/index', $data); // app/Views/Admin/Dashboard/index.php を指す
    }
}