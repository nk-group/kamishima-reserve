<?php

namespace App\Controllers\User;

use App\Controllers\BaseController; // CodeIgniter標準のBaseControllerを継承

class CalendarController extends BaseController
{
    public function index(): string
    {
        $data = [
            'page_title' => '予約状況確認カレンダー',
        ];

        // 利用者向けレイアウト (user-layout.php) を使い、
        // User/Calendar/index ビューをレンダリングする想定
        return view('User/Calendar/index', $data);
    }
}