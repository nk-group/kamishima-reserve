<?php

namespace App\Controllers\Customer;

use CodeIgniter\HTTP\ResponseInterface;

/**
 * 顧客向け予約コントローラー
 * Clear車検予約システムの顧客向け予約機能を提供します。
 */
class ReservationController extends BaseController
{
    protected $reservationModel;
    protected $timeSlotModel;
    protected $shopModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        // モデルを初期化
        $this->reservationModel = model('App\Models\ReservationModel');
        $this->timeSlotModel = model('App\Models\TimeSlotModel');
        $this->shopModel = model('App\Models\ShopModel');
    }

    /**
     * 予約フォーム表示
     */
    public function form()
    {
        try {
            // カレンダーから選択された日時を取得
            $selectedDate = $this->request->getGet('date');
            $selectedTimeSlotId = $this->request->getGet('time_slot_id');
            $shopId = $this->request->getGet('shop_id');
            
            // shop_id が未指定の場合、デフォルト店舗を選択してリダイレクト
            if (empty($shopId)) {
                try {
                    $defaultShopId = $this->shopModel->getDefaultClearShakenShopId();
                    $redirectUrl = site_url('customer/reservation/form') . '?shop_id=' . $defaultShopId;
                    if (!empty($selectedDate)) {
                        $redirectUrl .= '&date=' . $selectedDate;
                    }
                    if (!empty($selectedTimeSlotId)) {
                        $redirectUrl .= '&time_slot_id=' . $selectedTimeSlotId;
                    }
                    return redirect()->to($redirectUrl);
                } catch (\RuntimeException $e) {
                    return $this->showError('Clear車検対応店舗が見つかりません。', 503);
                }
            }
            
            // バリデーション
            if (!is_numeric($shopId)) {
                return $this->showError('無効な店舗IDです。', 400);
            }
            
            $shopId = (int)$shopId;

            // フォーム用データを準備
            $formData = $this->prepareFormData($selectedDate, $selectedTimeSlotId, $shopId);

            $data = [
                'page_title' => 'Clear車検予約フォーム | 上嶋自動車',
                'body_id' => 'page-customer-reservation-form',
                'selected_date' => $selectedDate,
                'selected_time_slot_id' => $selectedTimeSlotId,
                'shop_id' => $shopId,
                'available_time_slots' => $formData['available_time_slots'],
            ];

            return $this->render('Customer/Reservation/form', $data);

        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Reservation form error: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return $this->showError('予約フォームの表示に失敗しました。再度お試しください。', 500);
        }
    }

    /**
     * 予約データ送信処理
     */
    public function submit()
    {
        // POST以外は拒否
        if (!$this->request->is('post')) {
            return $this->showError('不正なリクエストです。', 405);
        }

        try {
            $postData = $this->request->getPost();

            // バリデーション
            $validationRules = $this->getCustomerValidationRules();
            $validationMessages = $this->getCustomerValidationMessages();

            if (!$this->validate($validationRules, $validationMessages)) {
                $errors = $this->validator->getErrors();
                log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Validation errors: ' . json_encode($errors));
                
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $errors);
            }

            // 予約データ整形
            $reservationData = $this->prepareCustomerReservationData($postData);

            // 予約保存
            $reservationId = $this->reservationModel->insert($reservationData);
            
            if ($reservationId) {
                // 予約確認URLを生成
                $reservation = $this->reservationModel->find($reservationId);
                $confirmationUrl = site_url('customer/reservation/status/' . $reservation->reservation_guid);
                
                // 成功ページまたは確認ページにリダイレクト
                return redirect()->to($confirmationUrl)
                    ->with('message', '予約を受け付けました。予約内容をご確認ください。');
            } else {
                // モデルのバリデーションエラーを取得
                $errors = $this->reservationModel->errors();
                log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Model validation errors: ' . json_encode($errors));
                
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $errors);
            }

        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Reservation submission failed: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', '予約の送信に失敗しました。再度お試しください。');
        }
    }

    /**
     * 予約状況確認ページ
     */
    public function status(string $guid = '')
    {
        try {
            if (empty($guid)) {
                return $this->showError('予約確認URLが無効です。', 404);
            }

            // GUIDで予約を検索
            $reservation = $this->reservationModel->where('reservation_guid', $guid)->first();
            
            if (!$reservation) {
                $data = [
                    'page_title' => '予約状況確認 | 上嶋自動車',
                    'body_id' => 'page-customer-reservation-status',
                    'reservation' => null,
                    'shop_phone' => '0155-24-2510', // 固定値（実際は設定から取得）
                ];
                
                return $this->render('Customer/Reservation/status', $data);
            }

            // 表示用データを準備
            $reservationData = $this->prepareStatusDisplayData($reservation);

            $data = [
                'page_title' => '予約状況確認 | 上嶋自動車',
                'body_id' => 'page-customer-reservation-status',
                'reservation' => $reservationData,
                'shop_phone' => '0155-24-2510', // 固定値（実際は設定から取得）
            ];

            return $this->render('Customer/Reservation/status', $data);

        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Reservation status error: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return $this->showError('予約状況の確認に失敗しました。再度お試しください。', 500);
        }
    }

    /**
     * 予約キャンセル処理
     */
    public function cancel()
    {
        // POST以外は拒否
        if (!$this->request->is('post')) {
            return $this->jsonResponse([
                'success' => false,
                'message' => '不正なリクエストです。'
            ], 405);
        }

        try {
            $guid = $this->request->getPost('reservation_guid');
            
            if (empty($guid)) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => '予約確認番号が無効です。'
                ], 400);
            }

            // 予約を検索
            $reservation = $this->reservationModel->where('reservation_guid', $guid)->first();
            
            if (!$reservation) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => '指定された予約が見つかりません。'
                ], 404);
            }

            // キャンセル可能かチェック
            if (!$this->canCancelReservation($reservation)) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'この予約はキャンセルできません。店舗までお問い合わせください。'
                ], 400);
            }

            // キャンセル処理（ステータス更新）
            $cancelledStatusId = 4; // キャンセル済みステータスID（実際の値に調整が必要）
            $result = $this->reservationModel->update($reservation->id, [
                'reservation_status_id' => $cancelledStatusId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if ($result) {
                return $this->jsonResponse([
                    'success' => true,
                    'message' => '予約をキャンセルしました。'
                ]);
            } else {
                throw new \RuntimeException('予約のキャンセル処理に失敗しました。');
            }

        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Reservation cancellation failed: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return $this->jsonResponse([
                'success' => false,
                'message' => 'キャンセル処理に失敗しました。再度お試しください。'
            ], 500);
        }
    }

    /**
     * フォーム用データを準備します。
     *
     * @param string|null $selectedDate 選択された日付 (Y-m-d形式)
     * @param string|null $selectedTimeSlotId 選択された時間帯ID
     * @param int $shopId 店舗ID
     * @return array フォーム表示用データの連想配列
     */
    private function prepareFormData(?string $selectedDate, ?string $selectedTimeSlotId, int $shopId): array
    {
        $availableTimeSlots = [];
        if (!empty($selectedDate)) {
            // 指定された店舗の有効な時間帯のみを取得
            $timeSlots = $this->timeSlotModel->where('shop_id', $shopId)
                                            ->where('active', 1)
                                            ->orderBy('start_time', 'ASC')
                                            ->findAll();
            
            foreach ($timeSlots as $timeSlot) {
                $availableTimeSlots[] = [
                    'id' => $timeSlot->id,
                    'display_time' => $timeSlot->start_time . ' - ' . $timeSlot->end_time,
                ];
            }
        }

        return [
            'available_time_slots' => $availableTimeSlots,
        ];
    }

    /**
     * 顧客向けバリデーションルール
     */
    private function getCustomerValidationRules(): array
    {
        return [
            'desired_date' => 'required|valid_date',
            'desired_time_slot_id' => 'required|integer|greater_than[0]',
            'customer_name' => 'required|max_length[50]',
            'customer_kana' => 'permit_empty|max_length[50]',
            'email' => 'required|valid_email|max_length[255]',
            'phone_number1' => 'required|max_length[20]',
            'vehicle_license_number' => 'required|max_length[10]',
            'vehicle_model_name' => 'required|max_length[100]',
            'first_registration_date' => 'permit_empty|valid_date',
            'shaken_expiration_date' => 'permit_empty|valid_date',
            'vehicle_type_id' => 'permit_empty|integer|greater_than[0]',
            'model_spec_number' => 'permit_empty|max_length[20]',
            'classification_number' => 'permit_empty|max_length[20]',
            'notes' => 'permit_empty|max_length[1000]',
        ];
    }

    /**
     * 顧客向けバリデーションメッセージ
     */
    private function getCustomerValidationMessages(): array
    {
        return [
            'desired_date' => [
                'required' => '希望日は必須です。',
                'valid_date' => '有効な日付を入力してください。',
            ],
            'desired_time_slot_id' => [
                'required' => '希望時間は必須です。',
                'integer' => '有効な時間帯を選択してください。',
                'greater_than' => '有効な時間帯を選択してください。',
            ],
            'customer_name' => [
                'required' => 'お名前は必須です。',
                'max_length' => 'お名前は50文字以内で入力してください。',
            ],
            'email' => [
                'required' => 'メールアドレスは必須です。',
                'valid_email' => '有効なメールアドレスを入力してください。',
                'max_length' => 'メールアドレスは255文字以内で入力してください。',
            ],
            'phone_number1' => [
                'required' => '電話番号は必須です。',
                'max_length' => '電話番号は20文字以内で入力してください。',
            ],
            'vehicle_license_number' => [
                'required' => '車両ナンバーは必須です。',
                'max_length' => '車両ナンバーは10文字以内で入力してください。',
            ],
            'vehicle_model_name' => [
                'required' => '車種は必須です。',
                'max_length' => '車種は100文字以内で入力してください。',
            ],
        ];
    }

    /**
     * 顧客向け予約データを整形
     */
    private function prepareCustomerReservationData(array $postData): array
    {
        $clearShakenWorkTypeId = 1; // Clear車検のID
        $defaultShopId = 1; // デフォルト店舗ID（実際は設定から取得）
        $defaultStatusId = 1; // デフォルト予約ステータスID（受付済み等）

        $data = [
            'reservation_guid' => $this->generateGuid(),
            'reservation_status_id' => $defaultStatusId,
            'work_type_id' => $clearShakenWorkTypeId,
            'shop_id' => $defaultShopId,
            'desired_date' => $postData['desired_date'] ?? null,
            'desired_time_slot_id' => !empty($postData['desired_time_slot_id']) ? (int)$postData['desired_time_slot_id'] : null,
            'customer_name' => $postData['customer_name'] ?? '',
            'customer_kana' => $postData['customer_kana'] ?? null,
            'email' => $postData['email'] ?? '',
            'phone_number1' => $postData['phone_number1'] ?? '',
            'vehicle_license_region' => $postData['vehicle_license_region'] ?? null,
            'vehicle_license_class' => $postData['vehicle_license_class'] ?? null,
            'vehicle_license_kana' => $postData['vehicle_license_kana'] ?? null,
            'vehicle_license_number' => $postData['vehicle_license_number'] ?? '',
            'vehicle_model_name' => $postData['vehicle_model_name'] ?? '',
            'first_registration_date' => $postData['first_registration_date'] ?? null,
            'shaken_expiration_date' => $postData['shaken_expiration_date'] ?? null,
            'vehicle_type_id' => !empty($postData['vehicle_type_id']) ? (int)$postData['vehicle_type_id'] : null,
            'model_spec_number' => $postData['model_spec_number'] ?? null,
            'classification_number' => $postData['classification_number'] ?? null,
            'notes' => $postData['notes'] ?? null,
            'via_line' => 0, // 顧客向けフォームはLINE経由ではない
        ];

        // 空文字列をNULLに変換
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }

        return $data;
    }

    /**
     * 予約状況確認用表示データを準備
     */
    private function prepareStatusDisplayData($reservation): object
    {
        // 必要な関連データを取得・整形してオブジェクトを作成
        $statusData = new \stdClass();
        
        // 基本情報
        $statusData->reservation_no = $reservation->reservation_no;
        $statusData->reservation_guid = $reservation->reservation_guid;
        $statusData->status_name = '受付済み'; // 実際はマスタから取得
        $statusData->status_class = 'confirmed'; // CSSクラス用
        $statusData->work_type_name = 'Clear車検';
        $statusData->shop_name = '上嶋自動車'; // 実際はマスタから取得
        
        // 日時情報
        $statusData->desired_date_display = date('Y年n月j日（G）', strtotime($reservation->desired_date));
        $statusData->time_slot_display = '9:00 - 12:00'; // 実際は時間帯マスタから取得
        
        // お客様情報
        $statusData->customer_name = $reservation->customer_name;
        $statusData->customer_kana = $reservation->customer_kana;
        $statusData->email = $reservation->email;
        $statusData->phone_number1 = $reservation->phone_number1;
        
        // 車両情報
        $statusData->vehicle_license_region = $reservation->vehicle_license_region;
        $statusData->vehicle_license_class = $reservation->vehicle_license_class;
        $statusData->vehicle_license_kana = $reservation->vehicle_license_kana;
        $statusData->vehicle_license_number = $reservation->vehicle_license_number;
        $statusData->vehicle_model_name = $reservation->vehicle_model_name;
        $statusData->shaken_expiration_date_display = $reservation->shaken_expiration_date ? 
            date('Y年n月j日', strtotime($reservation->shaken_expiration_date)) : null;
        
        // 備考
        $statusData->notes = $reservation->notes;
        
        // キャンセル可能判定
        $statusData->can_cancel = $this->canCancelReservation($reservation);
        
        return $statusData;
    }

    /**
     * 予約キャンセル可能判定
     */
    private function canCancelReservation($reservation): bool
    {
        // 予約日の前日までキャンセル可能とする
        $reservationDate = new \DateTime($reservation->desired_date);
        $today = new \DateTime();
        $today->setTime(23, 59, 59); // 当日の終了時刻
        
        return $reservationDate > $today;
    }

    /**
     * GUIDを生成
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