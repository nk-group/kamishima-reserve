<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController; // Admin用のBaseControllerを継承

class ReservationController extends BaseController
{
    /**
     * 予約一覧ページを表示します。
     */
    public function index()
    {
        $data = [
            'page_title' => '予約検索／一覧 | 車検予約管理システム',
            'body_id'    => 'page-admin-reservations-index',
            // ここで予約データをモデルから取得してビューに渡す処理を追加します。
            // 例: 'reservations' => $reservationModel->findAll(),
        ];

        // ビューファイル名を指定し、データを渡して表示
        // (ビューファイルは app/Views/Admin/Reservations/index.php とします)
        return $this->render('Admin/Reservations/index', $data);
    }

    /**
     * 新規予約入力フォームを表示します。
     */
    public function new()
    {
        $data = [
            'page_title' => '新規予約作成 | 車検予約管理システム',
            'body_id'    => 'page-admin-reservations-new',
        ]; // ビューに渡すデータを格納する配列

        // フォームヘルパーをロード (BaseControllerで既にロードしている場合は不要)
        helper('form');

        // プルダウンメニュー用のデータを準備します。
        // 実際のアプリケーションでは、これらのデータはモデルを介してデータベースから取得することを強く推奨します。
        $data['service_types'] = [
            ['id' => 'clear_shaken', 'name' => 'Clear車検'],
            ['id' => 'legal_inspection', 'name' => '法定点検'],
            ['id' => 'oil_change', 'name' => 'オイル交換'],
            // 他の作業種別を追加
        ];

        $data['shops'] = [
            ['id' => 'honsha_kojo', 'name' => '本社（車検・整備工場）'],
            ['id' => '○○_branch', 'name' => '〇〇支店'],
            // 他の店舗を追加
        ];

        $data['vehicle_types'] = [
            ['id' => 'normal_car', 'name' => '普通乗用車'],
            ['id' => 'kei_car', 'name' => '軽自動車'],
            // 他の車両種別を追加
        ];

        // 予約状況の選択肢 (新規作成時は「仮予約」などをデフォルトにすることを想定)
        $data['reservation_statuses'] = [
            ['id' => 'tentative', 'name' => '仮予約'],
            ['id' => 'confirmed', 'name' => '予約確定'],
        ];
        // 新規入力時の予約状況のデフォルト値 (例: 'tentative')
        $data['default_reservation_status'] = 'tentative';


        // ビューファイル名を指定し、データを渡して表示
        // (ビューファイルは app/Views/admin/Reservations/new.php とします)
        return view('Admin/Reservations/new', $data);
    }

    /**
     * フォームから送信された予約データを処理し、保存します。
     * 本番で使用するには、バリデーション、エラーハンドリング、モデルを通じたDB操作を実装する必要があります。
     * POSTリクエストでこのメソッドが呼ばれることを想定します。
     */
    public function create()
    {
        // --- 本番用の実装例 (要詳細化) ---
        // $validation =  \Config\Services::validation();
        // // バリデーションルールの設定 (例)
        // $rules = [
        //     'customer_name' => 'required|max_length[100]',
        //     'phone_number1' => 'required|max_length[15]',
        //     'reservation_date' => 'required|valid_date',
        //     // ... 他のフィールドのルール
        // ];

        // if (! $this->validate($rules)) {
        //     // バリデーションエラーの場合、エラーメッセージと共にフォームを再表示
        //     // return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        // }

        // // データベースへの保存処理 (モデルを使用)
        // // $reservationModel = new \App\Models\ReservationModel(); // 適切なモデルをインスタンス化
        // $postData = $this->request->getPost();

        // // ここで $postData をモデルに渡す前に整形・加工が必要な場合があります。

        // // if ($reservationModel->insert($postData)) { // モデルのinsertメソッドを呼び出し
        // //     // 成功した場合の処理 (例: 一覧ページへリダイレクトと成功メッセージ)
        // //     return redirect()->to(site_url('admin/reservations'))->with('message', '予約を登録しました。');
        // // } else {
        // //     // 失敗した場合の処理 (例: フォームへ戻りエラーメッセージ表示)
        // //     return redirect()->back()->withInput()->with('error', '予約の登録に失敗しました。データベースエラーが発生した可能性があります。');
        // // }
        // --- ここまで本番用の実装例 ---

        // 現時点では、完了メッセージを表示してリダイレクトする仮の処理
        session()->setFlashdata('message', '（仮）予約登録処理が呼び出されました。本番ではバリデーションとDB保存処理が必要です。');
        return redirect()->to(route_to('admin.reservations.new'));
    }

    /**
     * 予約詳細ページを表示します。
     *
     * @param int $id 予約ID
     * @return string|RedirectResponse
     */
    public function edit(int $id)
    {
        // 実際のアプリケーションでは、ここで$idを使ってデータベースから予約データを取得します。
        // 例: $reservation = $this->reservationModel->find($id);
        // データが見つからない場合は404エラーやリダイレクトを返します。
        // if (!$reservation) {
        //     throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        // }

        // 仮の予約データ
        $reservation = [
            'id' => $id,
            'reservation_number' => sprintf('%08d', $id),
            'status' => '予約確定',
            'service_type' => 'Clear車検',
            'desired_date' => '2025/2/5水',
            'start_time' => '09:30',
            'end_time' => '09:30',
            'shop' => '本社（車検・整備工場）',
            'customer_name' => '鈴木　一郎',
            'customer_kana' => 'スズキ　イチロウ',
            'email' => 'i.suzuki@sample.com',
            'line_via' => true,
            'line_display_name' => '鈴木',
            'phone1' => '090-XXXX-XXXX',
            'phone2' => '0155-XX-XXXX',
            'zip_code' => '080-0803',
            'address' => '帯広市東３条南８丁目１－１　ＮＫビル',
            'vehicle_plate_region' => '帯広',
            'vehicle_plate_class' => '330',
            'vehicle_plate_kana' => 'る',
            'vehicle_plate_number' => '583',
            'vehicle_model' => 'スカイライン',
            'first_registration' => '2025年2月',
            'inspection_due_date' => '2025年2月20日',
            'model_designation_no' => '50506',
            'classification_no' => '0689',
            'loaner_needed' => true,
            'loaner_car_name' => 'フィット1号車',
            'customer_request' => 'お客様からのご要望など',
            'memo' => '車検に関する注意事項等を記載します。',
            'next_inspection_date' => '2025年2月20日',
            'send_next_inspection_guide' => true,
            'reminder_email_sent' => true,
            'reminder_email_sent_date' => '----年-月-日 --:--',
            'created_at' => '2025年1月30日 20:15',
            'updated_at' => '2025年1月30日 20:15',
        ];

        $data = [
            'page_title' => '予約詳細 | 車検予約管理システム',
            'body_id'    => 'page-admin-reservations-detail',
            'reservation' => (object)$reservation, // ビューでオブジェクトとしてアクセスできるようにキャスト
        ];

        // ビューファイル名を指定し、データを渡して表示
        // (ビューファイルは app/Views/Admin/Reservations/detail.php とします)
        return $this->render('Admin/Reservations/detail', $data);
    }

    /**
     * 予約詳細フォームから送信されたデータを更新します。
     *
     * @param int $id 予約ID
     * @return RedirectResponse
     */
    public function update(int $id): RedirectResponse
    {
        // 実際のアプリケーションでは、ここでバリデーションとデータベース更新処理を行います。
        // 例: $this->reservationModel->update($id, $this->request->getPost());

        session()->setFlashdata('message', "予約ID {$id} の情報が更新されました。（仮）");
        return redirect()->to(route_to('admin.reservations.edit', $id));
    }
}