<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use App\Models\ShopClosingDayModel;
use App\Models\ShopModel;

class ShopClosingDayController extends BaseController
{
    protected $shopClosingDayModel;
    protected $shopModel;

    public function __construct()
    {
        $this->shopClosingDayModel = new ShopClosingDayModel();
        $this->shopModel = new ShopModel();
        
        // ヘルパーを読み込み
        helper(['form', 'url', 'shop_closing_day']);
    }

    /**
     * 定休日マスタ一覧画面
     */
    public function index()
    {
        // 検索パラメータを取得
        $filters = $this->getSearchFilters();
        
        // ページネーション設定
        $perPage = 20;
        $page = (int)($this->request->getGet('page') ?? 1);
        
        // データ取得
        if (!empty(array_filter($filters))) {
            // フィルター条件がある場合
            $closingDays = $this->shopClosingDayModel->searchClosingDays($filters);
            $total = count($closingDays);
            
            // 手動でページネーション
            $offset = ($page - 1) * $perPage;
            $closingDays = array_slice($closingDays, $offset, $perPage);
        } else {
            // 全件取得（ページネーション付き）
            $total = $this->shopClosingDayModel->countAllResults(false);
            $closingDays = $this->shopClosingDayModel
                ->orderBy('shop_id', 'ASC')
                ->orderBy('closing_date', 'ASC')
                ->paginate($perPage, 'default', $page);
        }
        
        // 店舗一覧を取得（フィルター用）
        $shops = get_shop_list_for_select();
        
        // ページネーション設定
        $pager = \Config\Services::pager();
        $pager->setPath('admin/shop-closing-days');
        
        $data = [
            'page_title' => '定休日マスタ',
            'h1_title' => '定休日マスタ管理',
            'closing_days' => $closingDays,
            'shops' => $shops,
            'filters' => $filters,
            'pager' => $pager,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'repeat_type_options' => $this->shopClosingDayModel::getRepeatTypeOptions()
        ];

        return view('Admin/ShopClosingDays/index', $data);
    }

    /**
     * 新規作成フォーム表示
     */
    public function new()
    {
        $data = [
            'page_title' => '定休日マスタ新規作成',
            'h1_title' => '定休日新規作成',
            'shops' => get_shop_list_for_select(),
            'repeat_type_options' => $this->shopClosingDayModel::getRepeatTypeOptions(),
            'form_data' => $this->getDefaultFormData(),
            'validation' => session()->get('validation')
        ];

        return view('Admin/ShopClosingDays/form', $data);
    }

    /**
     * 新規作成処理（デバッグ版）
     */
    public function create()
    {
        $postData = $this->request->getPost();
        
        // デバッグログ
        log_message('debug', 'CREATE - Post data: ' . json_encode($postData));
        
        // 重複チェック（デバッグ用）
        $existingCheck = $this->shopClosingDayModel
            ->where('shop_id', $postData['shop_id'])
            ->where('holiday_name', $postData['holiday_name'])
            ->first();
            
        log_message('debug', 'CREATE - Existing check result: ' . ($existingCheck ? 'FOUND: ' . json_encode($existingCheck->toArray()) : 'NOT FOUND'));
        
        // データ作成（モデルでバリデーション実行）
        $data = [
            'shop_id' => $postData['shop_id'],
            'holiday_name' => $postData['holiday_name'],
            'closing_date' => $postData['closing_date'],
            'repeat_type' => $postData['repeat_type'],
            'repeat_end_date' => !empty($postData['repeat_end_date']) ? $postData['repeat_end_date'] : null,
            'is_active' => $postData['is_active'] ?? 1
        ];
        
        log_message('debug', 'CREATE - Data to save: ' . json_encode($data));

        try {
            if ($this->shopClosingDayModel->save($data)) {
                log_message('debug', 'CREATE - Save successful');
                return redirect()->to('/admin/shop-closing-days')
                               ->with('success', '定休日を登録しました。');
            } else {
                // モデルのバリデーションエラーを取得
                $errors = $this->shopClosingDayModel->errors();
                log_message('debug', 'CREATE - Model validation errors: ' . json_encode($errors));
                return redirect()->back()
                               ->withInput()
                               ->with('errors', $errors);
            }
        } catch (\RuntimeException $e) {
            log_message('debug', 'CREATE - Runtime exception: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', $e->getMessage());
        }
    }

    /**
     * 編集フォーム表示
     */
    public function edit($id)
    {
        $closingDay = $this->shopClosingDayModel->find($id);
        
        if (!$closingDay) {
            return redirect()->to('/admin/shop-closing-days')
                           ->with('error', '指定された定休日が見つかりません。');
        }

        $data = [
            'page_title' => '定休日マスタ編集',
            'h1_title' => '定休日編集',
            'shops' => get_shop_list_for_select(),
            'repeat_type_options' => $this->shopClosingDayModel::getRepeatTypeOptions(),
            'form_data' => $closingDay->getFormData(),
            'validation' => session()->get('validation')
        ];

        return view('Admin/ShopClosingDays/form', $data);
    }

    /**
     * 更新処理（デバッグ版）
     */
    public function update($id)
    {
        $closingDay = $this->shopClosingDayModel->find($id);
        
        if (!$closingDay) {
            return redirect()->to('/admin/shop-closing-days')
                           ->with('error', '指定された定休日が見つかりません。');
        }

        $postData = $this->request->getPost();
        
        // デバッグログ
        log_message('debug', 'UPDATE - ID: ' . $id);
        log_message('debug', 'UPDATE - Post data: ' . json_encode($postData));
        log_message('debug', 'UPDATE - Current data: ' . json_encode($closingDay->toArray()));
        
        // 重複チェック（デバッグ用）- 自分以外
        $existingCheck = $this->shopClosingDayModel
            ->where('shop_id', $postData['shop_id'])
            ->where('holiday_name', $postData['holiday_name'])
            ->where('id !=', $id)
            ->first();
            
        log_message('debug', 'UPDATE - Existing check result (excluding self): ' . ($existingCheck ? 'FOUND: ' . json_encode($existingCheck->toArray()) : 'NOT FOUND'));
        
        // データ更新（モデルでバリデーション実行）
        $data = [
            'id' => $id, // バリデーション用
            'shop_id' => $postData['shop_id'],
            'holiday_name' => $postData['holiday_name'],
            'closing_date' => $postData['closing_date'],
            'repeat_type' => $postData['repeat_type'],
            'repeat_end_date' => !empty($postData['repeat_end_date']) ? $postData['repeat_end_date'] : null,
            'is_active' => $postData['is_active'] ?? 1
        ];
        
        log_message('debug', 'UPDATE - Data to save: ' . json_encode($data));

        try {
            if ($this->shopClosingDayModel->update($id, $data)) {
                log_message('debug', 'UPDATE - Update successful');
                return redirect()->to('/admin/shop-closing-days')
                               ->with('success', '定休日を更新しました。');
            } else {
                // モデルのバリデーションエラーを取得
                $errors = $this->shopClosingDayModel->errors();
                log_message('debug', 'UPDATE - Model validation errors: ' . json_encode($errors));
                return redirect()->back()
                               ->withInput()
                               ->with('errors', $errors);
            }
        } catch (\RuntimeException $e) {
            log_message('debug', 'UPDATE - Runtime exception: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', $e->getMessage());
        }
    }

    /**
     * 削除処理（論理削除）
     */
    public function delete($id)
    {
        $closingDay = $this->shopClosingDayModel->find($id);
        
        if (!$closingDay) {
            return redirect()->to('/admin/shop-closing-days')
                           ->with('error', '指定された定休日が見つかりません。');
        }

        if ($this->shopClosingDayModel->delete($id)) {
            return redirect()->to('/admin/shop-closing-days')
                           ->with('success', '定休日を削除しました。');
        } else {
            return redirect()->to('/admin/shop-closing-days')
                           ->with('error', '削除に失敗しました。');
        }
    }

    /**
     * 一括作成フォーム表示
     */
    public function batch()
    {
        $data = [
            'page_title' => '定休日一括作成',
            'h1_title' => '定休日一括作成',
            'shops' => get_shop_list_for_select(),
            'repeat_type_options' => $this->shopClosingDayModel::getRepeatTypeOptions(),
            'form_data' => $this->getDefaultBatchFormData(),
            'validation' => session()->get('validation')
        ];

        return view('Admin/ShopClosingDays/batch_form', $data);
    }

    /**
     * 一括作成処理
     */
    public function batchCreate()
    {
        $postData = $this->request->getPost();
        
        $shopId = $postData['shop_id'];
        $holidayName = $postData['holiday_name'];
        $startDate = $postData['start_date'];
        $endDate = $postData['end_date'];
        $repeatType = $postData['repeat_type'];
        $repeatEndDate = !empty($postData['repeat_end_date']) ? $postData['repeat_end_date'] : null;
        $isActive = $postData['is_active'] ?? 1;

        try {
            $result = $this->shopClosingDayModel->createBatchClosingDays(
                $shopId,
                $holidayName,
                $startDate,
                $endDate,
                $repeatType,
                $repeatEndDate,
                $isActive
            );

            if ($result['success']) {
                return redirect()->to('/admin/shop-closing-days')
                               ->with('success', $result['message']);
            } else {
                return redirect()->back()
                               ->withInput()
                               ->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', '一括作成に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: 指定日が休業日かチェック
     */
    public function checkClosingDay()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        $shopId = $this->request->getGet('shop_id');
        $date = $this->request->getGet('date');

        if (empty($shopId) || empty($date)) {
            return $this->response->setJSON(['error' => 'パラメータが不足しています。']);
        }

        $isClosingDay = $this->shopClosingDayModel->isClosingDay($shopId, $date);
        
        return $this->response->setJSON([
            'is_closing_day' => $isClosingDay,
            'date' => $date,
            'shop_id' => $shopId
        ]);
    }

    /**
     * 検索フィルターを取得
     */
    private function getSearchFilters(): array
    {
        return [
            'shop_id' => $this->request->getGet('shop_id'),
            'repeat_type' => $this->request->getGet('repeat_type'),
            'is_active' => $this->request->getGet('is_active'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'holiday_name' => $this->request->getGet('holiday_name')
        ];
    }

    /**
     * フォームのデフォルトデータを取得
     */
    private function getDefaultFormData(): array
    {
        return [
            'id' => null,
            'shop_id' => '',
            'holiday_name' => '',
            'closing_date' => '',
            'repeat_type' => $this->shopClosingDayModel::REPEAT_TYPE_NONE,
            'repeat_end_date' => '',
            'is_active' => 1
        ];
    }

    /**
     * 一括作成フォームのデフォルトデータを取得
     */
    private function getDefaultBatchFormData(): array
    {
        return [
            'shop_id' => '',
            'holiday_name' => '',
            'start_date' => '',
            'end_date' => '',
            'repeat_type' => $this->shopClosingDayModel::REPEAT_TYPE_NONE,
            'repeat_end_date' => '',
            'is_active' => 1
        ];
    }
}