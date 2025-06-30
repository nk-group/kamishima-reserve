<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use App\Models\ReservationModel;
use App\Models\ReserveStatusModel;
use App\Models\WorkTypeModel;
use App\Models\ShopModel;
use App\Models\TimeSlotModel;
use App\Models\VehicleTypeModel;
use App\Enums\WorkTypeCode; // WorkTypeCode を使用するために追加

class ReservationController extends BaseController
{
    protected $reservationModel;
    protected $reserveStatusModel;
    protected $workTypeModel;
    protected $shopModel;
    protected $timeSlotModel;
    protected $vehicleTypeModel;

    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
        $this->reserveStatusModel = new ReserveStatusModel();
        $this->workTypeModel = new WorkTypeModel();
        $this->shopModel = new ShopModel();
        $this->timeSlotModel = new TimeSlotModel();
        $this->vehicleTypeModel = new VehicleTypeModel();
    }

    /**
     * 予約一覧ページを表示します。
     */
    public function index()
    {
        helper(['form', 'pagination']);

        // 検索パラメータを取得
        $searchParams = $this->getSearchParams();
        $perPage = 20;

        // クエリビルダを準備
        $builder = $this->reservationModel;
        $this->reservationModel->buildSearchConditions($builder, $searchParams);

        // ソート条件
        $sort = $searchParams['sort'] ?? 'desired_date';
        $direction = $searchParams['direction'] ?? 'DESC';
        $allowedSortColumns = ['desired_date', 'reservation_no', 'customer_name', 'created_at', 'updated_at', 'reservation_status_id'];
        if (in_array($sort, $allowedSortColumns)) {
            $builder->orderBy($sort, $direction);
        } else {
            $builder->orderBy('desired_date', 'DESC');
        }
        $builder->orderBy('id', 'DESC'); // 2番目のソートキー

        // ページネーション付きでデータを取得
        $reservations = $builder->paginate($perPage);
        $pager = $this->reservationModel->pager;

        // 統計情報取得
        $statistics = $this->reservationModel->getStatistics($searchParams);

        // 統計情報にステータス名を追加
        $statusList = $this->reserveStatusModel->getListForForm();
        foreach ($statistics['by_status'] as &$stat) {
            $stat['status_name'] = $statusList[$stat['reservation_status_id']] ?? '不明';
        }
        unset($stat); // ループ後の参照を解除

        $data = [
            'page_title' => '予約検索／一覧 | 車検予約管理システム',
            'h1_title' => '予約検索／一覧',
            'body_id' => 'page-admin-reservations-index',
            
            // 検索結果
            'reservations' => $reservations,
            'pager' => $pager,
            'total' => $pager->getTotal(),
            'statistics' => $statistics,
            
            // 検索条件
            'search_params' => $searchParams,
            
            // フォーム用選択肢
            'work_types' => $this->workTypeModel->findActive(),
            'shops' => $this->shopModel->findActiveShops(),
            
            // クイック検索定義
            'quick_searches' => $this->getQuickSearchDefinitions(),
        ];

        return $this->render('Admin/Reservations/index', $data);
    }

    /**
     * 検索パラメータを取得・整理します。
     */
    private function getSearchParams(): array
    {
        $request = $this->request;
        
        $params = [
            'customer_name' => $request->getGet('customer_name'),
            'vehicle_number' => $request->getGet('vehicle_number'),
            'line_display_name' => $request->getGet('line_display_name'),
            'shop_id' => $request->getGet('shop_id'),
            'status_id' => $request->getGet('status_id'),
            'date_from' => $request->getGet('date_from'),
            'date_to' => $request->getGet('date_to'),
            'quick_search' => $request->getGet('quick_search'),
            'sort' => $request->getGet('sort'),
            'direction' => $request->getGet('direction'),
        ];

        // 作業種別複数選択の処理
        $workTypeIds = $request->getGet('work_type_ids');
        if ($workTypeIds) {
            if (is_array($workTypeIds)) {
                $params['work_type_ids'] = array_filter($workTypeIds, 'is_numeric');
            } else {
                $params['work_type_ids'] = [(int)$workTypeIds];
            }
        }

        // 空文字列をnullに変換
        return array_map(function($value) {
            return ($value === '' || $value === '0') ? null : $value;
        }, $params);
    }

    /**
     * クイック検索の定義を返します。
     */
    private function getQuickSearchDefinitions(): array
    {
        return [
            'today' => '本日の作業',
            'incomplete' => '未完了',
            'this_month_completed' => '今月整備完了予定',
            'main_shop' => '本社作業',
            'clear_shop' => 'Clear車検店作業',
        ];
    }

    /**
     * CSVエクスポート機能
     */
    public function exportCsv()
    {
        $searchParams = $this->getSearchParams();
        $exportData = $this->reservationModel->getExportData($searchParams);

        // CSVヘッダー
        $headers = [
            '予約番号', '予約状況', '予約希望日', '開始時刻', '終了時刻',
            'お客様氏名', 'カナ名', 'メールアドレス', 'LINE名', 'LINE経由',
            '電話番号1', '電話番号2', '郵便番号', '住所',
            '車両地域', '車両分類', '車両かな', '車両番号', '車種名',
            '初年度登録', '車検満了日', '型式指定番号', '類別区分番号',
            '代車利用', '代車名', 'お客様要望', 'メモ',
            '次回点検日', '次回作業種別', '次回連絡日', '次回案内送信', '案内送信済み',
            '登録日時', '更新日時'
        ];

        // ファイル名
        $filename = 'reservations_' . date('Ymd_His') . '.csv';

        // レスポンスヘッダー設定
        $this->response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        // CSVデータ作成
        $output = fopen('php://output', 'w');
        
        // BOMを追加（Excel対応）
        fputs($output, "\xEF\xBB\xBF");
        
        // ヘッダー出力
        fputcsv($output, $headers);

        // データ出力
        foreach ($exportData as $row) {
            $csvRow = [
                $row['reservation_no'],
                $row['status_name'] ?? '',
                $row['desired_date'],
                $row['reservation_start_time'],
                $row['reservation_end_time'],
                $row['customer_name'],
                $row['customer_kana'] ?? '',
                $row['email'],
                $row['line_display_name'] ?? '',
                $row['via_line'] ? 'はい' : 'いいえ',
                $row['phone_number1'],
                $row['phone_number2'] ?? '',
                $row['postal_code'] ?? '',
                $row['address'] ?? '',
                $row['vehicle_license_region'] ?? '',
                $row['vehicle_license_class'] ?? '',
                $row['vehicle_license_kana'] ?? '',
                $row['vehicle_license_number'],
                $row['vehicle_model_name'] ?? '',
                $row['first_registration_date'] ?? '',
                $row['shaken_expiration_date'] ?? '',
                $row['model_spec_number'] ?? '',
                $row['classification_number'] ?? '',
                $row['loaner_usage'] ? 'はい' : 'いいえ',
                $row['loaner_name'] ?? '',
                $row['customer_requests'] ?? '',
                $row['notes'] ?? '',
                $row['next_inspection_date'] ?? '',
                $row['next_work_type_name'] ?? '',
                $row['next_contact_date'] ?? '',
                $row['send_inspection_notice'] ? 'はい' : 'いいえ',
                $row['inspection_notice_sent'] ? 'はい' : 'いいえ',
                $row['created_at'],
                $row['updated_at']
            ];
            
            fputcsv($output, $csvRow);
        }

        fclose($output);
        return $this->response;
    }

    /**
     * 新規予約入力フォームを表示します。
     */
    public function new()
    {
        helper(['form', 'app_form']);

        $data = [
            'page_title' => '新規予約作成 | 車検予約管理システム',
            'h1_title' => '新規予約作成',
            'body_id' => 'page-admin-reservations-new',
            'reservation' => null, // 新規なのでnull
            'form_action' => route_to('admin.reservations.create'),
            'is_edit' => false,
        ];

        // フォーム用データを準備
        $data = array_merge($data, $this->prepareFormData());

        return $this->render('Admin/Reservations/new', $data);
    }

    /**
     * フォームから送信された予約データを処理し、保存します。
     */
    public function create()
    {
        helper(['form', 'app_form']);

        $postData = $this->request->getPost();
        
        // データ整形
        $reservationData = $this->prepareReservationData($postData);

        try {
            // デバッグ用ログ
            log_message('debug', 'Reservation data to insert: ' . json_encode($reservationData));
            
            $reservationId = $this->reservationModel->insert($reservationData);
            
            if ($reservationId) {
                return redirect()->to(route_to('admin.reservations.edit', $reservationId))
                    ->with('message', '予約を登録しました。');
            } else {
                // モデルのバリデーションエラーを取得
                $errors = $this->reservationModel->errors();
                log_message('error', 'Model validation errors: ' . json_encode($errors));
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $errors);
            }
        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Reservation creation failed: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', '予約の登録に失敗しました。再度お試しください。');
        }
    }

    /**
     * 予約詳細/編集ページを表示します。
     */
    public function edit(int $id)
    {
        helper(['form', 'app_form']);

        $reservation = $this->reservationModel->find($id);
        
        if (!$reservation) {
            return redirect()->to(route_to('admin.reservations.index'))
                ->with('error', '指定された予約が見つかりません。');
        }

        $data = [
            'page_title' => '予約詳細・編集 | 車検予約管理システム',
            'h1_title' => '予約詳細・編集',
            'body_id' => 'page-admin-reservations-detail',
            'reservation' => $reservation,
            'form_action' => route_to('admin.reservations.update', $id),
            'is_edit' => true,
        ];

        // フォーム用データを準備
        $data = array_merge($data, $this->prepareFormData());

        return $this->render('Admin/Reservations/edit', $data);
    }

    /**
     * 予約詳細フォームから送信されたデータを更新します。
     */
    public function update(int $id)
    {
        helper(['form', 'app_form']);

        $reservation = $this->reservationModel->find($id);
        
        if (!$reservation) {
            return redirect()->to(route_to('admin.reservations.index'))
                ->with('error', '指定された予約が見つかりません。');
        }

        $postData = $this->request->getPost();
        
        // データ整形
        $reservationData = $this->prepareReservationData($postData);

        try {
            $result = $this->reservationModel->update($id, $reservationData);
            
            if ($result) {
                return redirect()->to(route_to('admin.reservations.edit', $id))
                    ->with('message', '予約情報を更新しました。');
            } else {
                // モデルのバリデーションエラーを取得
                $errors = $this->reservationModel->errors();
                log_message('error', 'Model validation errors: ' . json_encode($errors));
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $errors);
            }
        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Reservation update failed: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', '予約の更新に失敗しました。再度お試しください。');
        }
    }

    /**
     * 予約を削除します。
     */
    public function delete(int $id)
    {
        $reservation = $this->reservationModel->find($id);
        
        if (!$reservation) {
            return redirect()->to(route_to('admin.reservations.index'))
                ->with('error', '指定された予約が見つかりません。');
        }

        try {
            $result = $this->reservationModel->delete($id); // 物理削除
            
            if ($result) {
                return redirect()->to(route_to('admin.reservations.index'))
                    ->with('message', '予約を削除しました。');
            } else {
                throw new \Exception('データベースからの削除に失敗しました。');
            }
        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Reservation deletion failed: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return redirect()->to(route_to('admin.reservations.index'))
                ->with('error', '予約の削除に失敗しました。再度お試しください。');
        }
    }

    /**
     * フォーム用の選択肢データを準備します。
     */
    private function prepareFormData(): array
    {
        // 時間帯データを全店舗分取得
        $timeSlots = $this->timeSlotModel->orderBy('shop_id')->orderBy('sort_order')->findAll();
        $shops = $this->shopModel->findActiveShops();
        
        // デバッグ用ログ
        log_message('debug', 'Time slots count: ' . count($timeSlots));
        log_message('debug', 'Shops count: ' . count($shops));
        
        // 店舗と時間帯の関係をログ出力
        foreach ($shops as $shop) {
            $shopTimeSlots = array_filter($timeSlots, function($ts) use ($shop) {
                return $ts->shop_id == $shop->id;
            });
            log_message('debug', "Shop ID {$shop->id} ({$shop->name}) has " . count($shopTimeSlots) . " time slots");
        }
        
        return [
            'reservation_statuses' => $this->reserveStatusModel->getListForForm(),
            'work_types' => $this->workTypeModel->findActive(),
            'shops' => $shops,
            'time_slots' => $timeSlots, // 全店舗の時間帯（JS側で絞り込み）
            // 車両種別は削除（画面から削除するため）
            'default_reservation_status' => $this->reserveStatusModel->getIdByCode('pending'), // 未確定をデフォルト
        ];
    }

    /**
     * POSTデータを予約保存用に整形します。
     */
    private function prepareReservationData(array $postData): array
    {
        $data = [
            'reservation_status_id' => !empty($postData['reservation_status_id']) ? (int)$postData['reservation_status_id'] : null,
            'work_type_id' => !empty($postData['work_type_id']) ? (int)$postData['work_type_id'] : null,
            'shop_id' => !empty($postData['shop_id']) ? (int)$postData['shop_id'] : null,
            'desired_date' => $postData['desired_date'] ?? null,
            'customer_name' => $postData['customer_name'] ?? null,
            'customer_kana' => $postData['customer_kana'] ?? null,
            'email' => $postData['email'] ?? null,
            'line_display_name' => $postData['line_display_name'] ?? null,
            'via_line' => !empty($postData['via_line']) ? 1 : 0,
            'phone_number1' => $postData['phone_number1'] ?? null,
            'phone_number2' => $postData['phone_number2'] ?? null,
            'postal_code' => $postData['postal_code'] ?? null,
            'address' => $postData['address'] ?? null,
            'vehicle_license_region' => $postData['vehicle_license_region'] ?? null,
            'vehicle_license_class' => $postData['vehicle_license_class'] ?? null,
            'vehicle_license_kana' => $postData['vehicle_license_kana'] ?? null,
            'vehicle_license_number' => $postData['vehicle_license_number'] ?? null,
            'vehicle_model_name' => $postData['vehicle_model_name'] ?? null,
            'first_registration_date' => $postData['first_registration_date'] ?? null,
            'shaken_expiration_date' => $postData['shaken_expiration_date'] ?? null,
            'model_spec_number' => $postData['model_spec_number'] ?? null,
            'classification_number' => $postData['classification_number'] ?? null,
            'loaner_usage' => !empty($postData['loaner_usage']) ? 1 : 0,
            'loaner_name' => $postData['loaner_name'] ?? null,
            'customer_requests' => $postData['customer_requests'] ?? null,
            'notes' => $postData['notes'] ?? null,
            'next_inspection_date' => $postData['next_inspection_date'] ?? null,
            'next_work_type_id' => !empty($postData['next_work_type_id']) ? (int)$postData['next_work_type_id'] : null,
            'next_contact_date' => $postData['next_contact_date'] ?? null,
            'send_inspection_notice' => !empty($postData['send_inspection_notice']) ? 1 : 0,
            'inspection_notice_sent' => !empty($postData['inspection_notice_sent']) ? 1 : 0,
        ];

        // 時間帯処理（既存ロジック）
        $data['desired_time_slot_id'] = !empty($postData['desired_time_slot_id']) ? (int)$postData['desired_time_slot_id'] : null;
        
        if (!empty($postData['reservation_start_time']) || !empty($postData['reservation_end_time'])) {
            $data['reservation_start_time'] = !empty($postData['reservation_start_time']) ? $postData['reservation_start_time'] : null;
            $data['reservation_end_time'] = !empty($postData['reservation_end_time']) ? $postData['reservation_end_time'] : null;
        }

        // 新規作成時にのみreservation_guidを生成
        if (!isset($postData['id']) || empty($postData['id'])) {
            $data['reservation_guid'] = $this->generateGuid();
        }

        // 空文字列を明示的にNULLに変換（データベース制約対応）
        foreach ($data as $key => $value) {
            if ($value === '' || $value === '0') {
                // 外部キーフィールドで空文字列や'0'の場合はNULLに変換
                if (in_array($key, ['desired_time_slot_id', 'reservation_status_id', 'work_type_id', 'shop_id', 'next_work_type_id'])) {
                    $data[$key] = null;
                }
            }
        }

        return $data;
    }

    /**
     * GUIDを生成します。
     */
    private function generateGuid(): string
    {
        // UUID v4を生成（簡易版）
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}