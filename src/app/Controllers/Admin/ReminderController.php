<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use App\Models\ReservationModel;
use App\Models\ShopModel;
use App\Models\WorkTypeModel;
// use App\Models\ReminderLogModel; // 将来的に作成するモデル

class ReminderController extends BaseController
{
    protected $reservationModel;
    protected $shopModel;
    protected $workTypeModel;

    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
        $this->shopModel = new ShopModel();
        $this->workTypeModel = new WorkTypeModel();
        // $this->reminderLogModel = new ReminderLogModel(); // 将来的に使用
        helper(['form', 'pagination']);
    }

    /**
     * リマインドメール送信予定／結果一覧
     */
    public function index()
    {
        $perPage = 20;
        $filters = $this->getFilters();
        $sort = $this->request->getGet('sort') ?? 'sent_at';
        $direction = $this->request->getGet('direction') ?? 'desc';

        $sortableColumns = ['sent_at', 'customer_name', 'next_inspection_date'];

        // クエリビルダを準備
        // ReservationModel を基点に LEFT JOIN して「未送信」も一覧に含める
        $builder = $this->reservationModel
            ->select('reservations.*, reminder_logs.status as reminder_status, reminder_logs.sent_at, reminder_logs.error_message')
            ->join('reminder_logs', 'reminder_logs.reservation_id = reservations.id', 'left');

        // 基本的な絞り込み（リマインド対象の予約）
        $builder->where('reservations.send_inspection_notice', 1)
                ->where('reservations.next_inspection_date IS NOT NULL');

        // 検索条件の適用
        if (!empty($filters['send_status'])) {
            if ($filters['send_status'] === 'pending') {
                $builder->where('reminder_logs.id IS NULL');
            } else {
                $builder->where('reminder_logs.status', $filters['send_status']);
            }
        }
        if (!empty($filters['customer_name'])) {
            $builder->like('reservations.customer_name', $filters['customer_name']);
        }
        if (!empty($filters['vehicle_number'])) {
            $builder->like('reservations.vehicle_license_number', $filters['vehicle_number']);
        }
        if (!empty($filters['shop_id'])) {
            $builder->where('reservations.shop_id', $filters['shop_id']);
        }
        if (!empty($filters['work_type_id'])) {
            $builder->where('reservations.next_work_type_id', $filters['work_type_id']);
        }
        if (!empty($filters['next_inspection_date_from'])) {
            $builder->where('reservations.next_inspection_date >=', $filters['next_inspection_date_from']);
        }
        if (!empty($filters['next_inspection_date_to'])) {
            $builder->where('reservations.next_inspection_date <=', $filters['next_inspection_date_to']);
        }

        // ソート順: 送信済みのものは送信日時、未送信のものは予定日でソート
        if ($sort === 'sent_at') {
            $sortColumn = "COALESCE(reminder_logs.sent_at, DATE_SUB(reservations.next_inspection_date, INTERVAL 30 DAY))";
            // 第3引数に false を指定して、式をエスケープしないようにする
            $builder->orderBy($sortColumn, $direction, false);
        } elseif (in_array($sort, $sortableColumns)) {
            $builder->orderBy('reservations.' . $sort, $direction);
        }

        // ページネーション付きでデータを取得
        $reminders = $builder->paginate($perPage);
        $pager = $this->reservationModel->pager;

        $data = [
            'page_title' => 'リマインドメール送信予定一覧',
            'h1_title' => 'リマインドメール送信予定一覧',
            'body_id' => 'page-admin-reminders-index', // JS読み込み用
            'reminders' => $reminders,
            'pager' => $pager,
            'total' => $pager->getTotal(),
            'per_page' => $perPage,
            'filters' => $filters,
            // フォーム用の選択肢
            'shops' => $this->shopModel->findActiveShops(),
            'work_types' => $this->workTypeModel->findActive(),
            'send_status_options' => [
                '' => 'すべて',
                'pending' => '未送信',
                'success' => '送信成功',
                'failed' => '送信失敗',
            ],
        ];

        return $this->render('Admin/Reminders/index', $data);
    }

    /**
     * 検索フィルターを取得
     */
    private function getFilters(): array
    {
        $filters = $this->request->getGet([
            'send_status', 'customer_name', 'vehicle_number', 'shop_id',
            'work_type_id', 'next_inspection_date_from', 'next_inspection_date_to'
        ]);

        // 空の値をフィルタから除去
        return array_filter($filters, fn($value) => $value !== null && $value !== '');
    }
}