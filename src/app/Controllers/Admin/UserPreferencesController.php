<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class UserPreferencesController extends BaseController
{
    public function __construct()
    {
        helper(['form', 'url']);
    }

    /**
     * 個人設定取得API
     * 
     * @return ResponseInterface JSON レスポンス
     */
    public function index(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        try {
            // 現在の個人設定を取得（セッションから）
            $preferences = session('user_preferences') ?? [];
            
            // 店舗一覧を取得
            $shops = get_shop_list_for_select();

            return $this->response->setJSON([
                'success' => true,
                'preferences' => $preferences,
                'shops' => $shops
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Failed to load user preferences: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => '設定の読み込みに失敗しました。'
            ]);
        }
    }

    /**
     * 個人設定保存API
     * 
     * @return ResponseInterface JSON レスポンス
     */
    public function save(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        try {
            $postData = $this->request->getPost();
            
            // バリデーション
            $rules = [
                'default_shop_id' => 'permit_empty|integer',
                'pagination_per_page' => 'permit_empty|integer|greater_than[0]|less_than_equal_to[100]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'バリデーションエラーが発生しました。',
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // 設定をセッションに保存
            $preferences = [
                'default_shop_id' => $postData['default_shop_id'] ?? null,
                'pagination_per_page' => $postData['pagination_per_page'] ?? 20
            ];

            session()->set('user_preferences', $preferences);

            return $this->response->setJSON([
                'success' => true,
                'message' => '設定を保存しました。'
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Failed to save user preferences: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => '設定の保存に失敗しました。'
            ]);
        }
    }
}