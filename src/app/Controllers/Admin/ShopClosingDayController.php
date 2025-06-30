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
        $perPage = 20;

        // 検索条件
        $filters = [
            'shop_id' => $this->request->getGet('shop_id'),
            'holiday_name' => $this->request->getGet('holiday_name'),
            'repeat_type' => $this->request->getGet('repeat_type'),
            'is_active' => $this->request->getGet('is_active'),
        ];
        // 空の値をフィルタから除去
        $cleanFilters = array_filter($filters, fn($value) => $value !== null && $value !== '');

        // モデルのクエリビルダを準備
        $builder = $this->shopClosingDayModel;

        if (!empty($cleanFilters['shop_id'])) {
            $builder->where('shop_id', $cleanFilters['shop_id']);
        }
        if (!empty($cleanFilters['holiday_name'])) {
            $builder->like('holiday_name', $cleanFilters['holiday_name']);
        }
        if (isset($cleanFilters['repeat_type']) && $cleanFilters['repeat_type'] !== '') {
            $builder->where('repeat_type', $cleanFilters['repeat_type']);
        }
        if (isset($cleanFilters['is_active']) && $cleanFilters['is_active'] !== '') {
            $builder->where('is_active', $cleanFilters['is_active']);
        }
        
        // ソート順
        $builder->orderBy('shop_id', 'ASC')->orderBy('closing_date', 'ASC');

        // ページネーション付きでデータを取得
        $closingDays = $builder->paginate($perPage);
        $pager = $this->shopClosingDayModel->pager;
        
        $data = [
            'closing_days' => $closingDays,
            'shops' => get_shop_list_for_select(),
            'filters' => $filters,
            'pager' => $pager,
            'total' => $pager->getTotal(),
            'per_page' => $perPage,
            'repeat_type_options' => $this->shopClosingDayModel::getRepeatTypeOptions()
        ];

        return $this->render('Admin/ShopClosingDays/index', $data);
    }

    /**
     * 新規作成フォーム表示
     */
    public function new()
    {
        $data = [
            'page_title' => '定休日マスタ新規作成',
            'h1_title' => '定休日新規作成',
            'body_id' => 'page-admin-shop-closing-days-form',
            'shops' => get_shop_list_for_select(),
            'repeat_type_options' => $this->shopClosingDayModel::getRepeatTypeOptions(),
            'form_data' => $this->getDefaultFormData(),
            'validation' => session()->get('validation')
        ];

        return $this->render('Admin/ShopClosingDays/form', $data);
    }

    /**
     * 新規作成処理
     */
    public function create()
    {
        $postData = $this->request->getPost();
        
        // データ作成（モデルでバリデーション実行）
        $data = [
            'shop_id' => $postData['shop_id'],
            'holiday_name' => $postData['holiday_name'],
            'closing_date' => $postData['closing_date'],
            'repeat_type' => $postData['repeat_type'],
            'repeat_end_date' => !empty($postData['repeat_end_date']) ? $postData['repeat_end_date'] : null,
            'is_active' => $postData['is_active'] ?? 1
        ];

        try {
            if ($this->shopClosingDayModel->save($data)) {
                return redirect()->to(route_to('admin.shop-closing-days.index'))
                               ->with('success', '定休日を登録しました。');
            } else {
                // モデルのバリデーションエラーを取得
                $errors = $this->shopClosingDayModel->errors();
                return redirect()->back()
                               ->withInput()
                               ->with('errors', $errors);
            }
        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Closing day creation failed: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', '定休日の登録に失敗しました。再度お試しください。');
        }
    }

    /**
     * 編集フォーム表示
     */
    public function edit($id)
    {
        $closingDay = $this->shopClosingDayModel->find($id);
        
        if (!$closingDay) {
            return redirect()->to(route_to('admin.shop-closing-days.index'))
                           ->with('error', '指定された定休日が見つかりません。');
        }

        $data = [
            'page_title' => '定休日マスタ編集',
            'h1_title' => '定休日編集',
            'body_id' => 'page-admin-shop-closing-days-form',
            'shops' => get_shop_list_for_select(),
            'repeat_type_options' => $this->shopClosingDayModel::getRepeatTypeOptions(),
            'form_data' => $closingDay->getFormData(),
            'validation' => session()->get('validation')
        ];

        return $this->render('Admin/ShopClosingDays/form', $data);
    }

    /**
     * 更新処理
     */
    public function update($id)
    {
        $closingDay = $this->shopClosingDayModel->find($id);
        
        if (!$closingDay) {
            return redirect()->to(route_to('admin.shop-closing-days.index'))
                           ->with('error', '指定された定休日が見つかりません。');
        }

        $postData = $this->request->getPost();
        
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

        try {
            if ($this->shopClosingDayModel->update($id, $data)) {
                return redirect()->to(route_to('admin.shop-closing-days.index'))
                               ->with('success', '定休日を更新しました。');
            } else {
                // モデルのバリデーションエラーを取得
                $errors = $this->shopClosingDayModel->errors();
                return redirect()->back()
                               ->withInput()
                               ->with('errors', $errors);
            }
        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Closing day update failed: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', '定休日の更新に失敗しました。再度お試しください。');
        }
    }

    /**
     * 削除処理（論理削除）
     */
    public function delete($id)
    {
        $closingDay = $this->shopClosingDayModel->find($id);
        
        if (!$closingDay) {
            return redirect()->to(route_to('admin.shop-closing-days.index'))
                           ->with('error', '指定された定休日が見つかりません。');
        }

        try {
            if ($this->shopClosingDayModel->delete($id)) {
                return redirect()->to(route_to('admin.shop-closing-days.index'))
                               ->with('success', '定休日を削除しました。');
            } else {
                throw new \Exception('データベースからの削除に失敗しました。');
            }
        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Closing day deletion failed: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return redirect()->to(route_to('admin.shop-closing-days.index'))
                           ->with('error', '定休日の削除に失敗しました。再度お試しください。');
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
            'body_id' => 'page-admin-shop-closing-days-batch',
            'shops' => get_shop_list_for_select(),
            'repeat_type_options' => $this->shopClosingDayModel::getRepeatTypeOptions(),
            'form_data' => $this->getDefaultBatchFormData(),
            'validation' => session()->get('validation')
        ];

        return $this->render('Admin/ShopClosingDays/batch_form', $data);
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
                return redirect()->to(route_to('admin.shop-closing-days.index'))
                               ->with('success', $result['message']);
            } else {
                return redirect()->back()
                               ->withInput()
                               ->with('error', $result['message']);
            }
        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Batch creation failed: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', '一括作成に失敗しました。再度お試しください。');
        }
    }

    /**
     * AJAX: 指定日が休業日かチェック
     * 
     * 注意：このメソッドは他の機能（予約システム等）で使用されている可能性があります。
     * 削除する前に、プロジェクト全体でこのメソッドが参照されていないことを確認してください。
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