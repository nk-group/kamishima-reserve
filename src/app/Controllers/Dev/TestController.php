<?php

namespace App\Controllers\Dev;


use App\Controllers\BaseController;
use CodeIgniter\I18n\Time; // Timeクラスの利用例のため
// 必要に応じてテストしたいモデルやライブラリをuse
use App\Models\ShopModel;
use App\Libraries\PdfService;


class TestController extends BaseController
{
    /**
     * このコントローラーが開発環境でのみアクセス可能であることを保証します。
     * より確実には、ルーティングレベルでの制御も推奨します。
     */
    public function __construct()
    {
        if (ENVIRONMENT !== 'development') {
            // 本番環境などでは404エラーを返すか、アクセス禁止処理
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        //helper(['auth_utility', 'app_form']); // 作成したヘルパーをロード
    }



    public function index()
    {
        // testAuthHelperと同様に、indexもビューを使う形に修正推奨
        $data = [
            'page_title' => '開発用テストコントローラー',
        ];
        $links = [
            ['url' => site_url('dev-test/auth'), 'title' => 'Auth Utility Helper テスト'],
            ['url' => site_url('dev-test/pdf-sample'), 'title' => 'PDF Service テスト (サンプルHTML)'],
            ['url' => site_url('dev-test/flatpickr-form'), 'title' => 'Flatpickr Helper テスト (フォーム要素表示)'],
            ['url' => site_url('dev-test/lab'), 'title' => '汎用テストLab'],
        ];
        $viewData = array_merge($data, ['links' => $links]);
        return view('Dev/index', $viewData); // test_index_view.php を別途作成推奨
     }



    /**
     * 汎用的なテストやデバッグコードを実行するためのサンドボックス。
     * このメソッド内にテストしたいコードを記述し、結果を $output 配列に格納します。
     */
    public function lab()
    {
        $pageTitle = '汎用テストサンドボックス';
        $outputResults = []; // 実行結果を格納する配列
        $executedCodeDescription = ''; // 実行したコードの簡単な説明

        // ==================================================================
        // ↓↓↓ このブロック内にテストしたいPHPコードを記述してください ↓↓↓
        // ==================================================================

        $executedCodeDescription = "サンプル: 現在時刻の表示とShopModelの簡単なテスト";

        // 簡単な変数の値を確認
        $outputResults['current_datetime'] = Time::now()->toDateTimeString();
        $outputResults['php_version'] = phpversion();

        // timezoneの確認
        $outputResults['timezone_test'] =
            "デフォルトタイムゾーン: " . date_default_timezone_get() . "\n" .
            "現在時刻 (date関数): " . date('Y-m-d H:i:s e P') . "\n" .
            "php.iniのdate.timezone設定: " . ini_get('date.timezone') . "\n";



        // モデルを使ったDBアクセステスト
        try {
            $shopModel = model(ShopModel::class);
            $outputResults['active_shops_count'] = count($shopModel->findActiveShops());
            $outputResults['shop_id_1_details'] = $shopModel->find(1);
            $outputResults['all_shops_sorted'] = $shopModel->orderBy('sort_order', 'ASC')->findAll();
        } catch (\Throwable $e) {
            $outputResults['shop_model_error'] = "エラー: " . $e->getMessage();
        }
        
        // 例3: 特定のヘルパー関数やライブラリメソッドの呼び出し
        // $outputResults['is_admin_check_in_sandbox'] = is_admin();
        // try {
        //     $pdfService = new PdfService();
        //     $outputResults['pdf_service_initialized'] = ($pdfService instanceof PdfService);
        // } catch (\Throwable $e) {
        //     $outputResults['pdf_service_error'] = "エラー: " . $e->getMessage();
        // }


        // ==================================================================
        // ↑↑↑ テストコードの記述はここまで ↑↑↑
        // ==================================================================

        $viewData = [
            'page_title'                => $pageTitle,
            'executed_code_description' => $executedCodeDescription,
            'outputResults'             => $outputResults, // 'output' から 'outputResults' に変更 (より明確な名前に)
        ];

        return view('Dev/test_lab', $viewData);
    }
    


    /**
     * Auth Utility Helper のテスト
     */
    public function testAuthHelper()
    {
        /** @var User|null $user */
        $user = current_user_entity(); // auth_utility_helper.php で定義した関数

        $viewData = [
            'page_title'           => 'Auth Helper テスト', // レイアウトに渡すページタイトル
            'isLoggedIn'           => auth()->loggedIn(),
            'user'                 => $user,
            'isAdminHelper'        => is_admin(), // auth_utility_helper.php で定義した関数
            'isStaffHelper'        => is_staff(), // auth_utility_helper.php で定義した関数
            'canAdminAccessHelper' => user_can('admin.access'), // auth_utility_helper.php で定義した関数
            'userGroups'           => $user ? $user->getGroups() : [],
        ];

        return view('Dev/test_auth_helper', $viewData);
    }


    /**
     * PdfService のテスト (サンプルHTMLから生成)
     */
    public function testPdfService()
    {
        try {
            $pdfService = new PdfService(); // または service('pdfService')
            $html = "<h1>PDF生成テスト</h1>";
            $html .= "<p>これは <strong>mPDF</strong> と作成した <code>PdfService</code> を使って生成されたサンプルPDFです。</p>";
            $html .= "<p>現在時刻: " . date('Y-m-d H:i:s') . "</p>";
            $html .= "<p style='font-family:ipaexg;'>日本語フォントテスト: こんにちは世界！ (Hello World!)</p>"; // 日本語と英語の混在テスト

            // PDFコンテンツを文字列として取得 (出力モード 'S')
            $pdfContent = $pdfService->generateFromHtml($html, 'sample_document.pdf', 'S');

            // CodeIgniterのレスポンスオブジェクトを使ってPDFを返す
            return $this->response
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setHeader('Content-Disposition', 'inline; filename="sample_document.pdf"') // インライン表示の場合
                        // ->setHeader('Content-Disposition', 'attachment; filename="sample_document.pdf"') // ダウンロードさせたい場合
                        ->setBody($pdfContent)
                        ->send();

        } catch (\Exception $e) {
            // エラーページやエラーログの処理は元のまま
            $output = "<h2>PDF生成エラー</h2>";
            $output .= "<p>エラーメッセージ: " . esc($e->getMessage()) . "</p>";
            $output .= "<pre>" . esc($e->getTraceAsString()) . "</pre>";
            $output .= "<hr><a href='" . site_url('dev-test') . "'>テスト一覧に戻る</a>";
            return $output; // エラーメッセージをHTMLとして表示
        }
    }



    /**
     * PdfService のテスト (予約タグ - 要ReservationModelとデータ)
     * @param int $reservationId
     */
    // public function testReservationTagPdf(int $reservationId)
    // {
    //     // このテストを実行するには、ReservationModelと実際の予約データが必要です。
    //     // $reservationModel = model('App\Models\ReservationModel');
    //     // $reservation = $reservationModel->find($reservationId);
    //     // if (!$reservation) {
    //     //     echo "予約ID: {$reservationId} が見つかりません。";
    //     //     echo "<hr><a href='" . site_url('dev-test') . "'>テスト一覧に戻る</a>";
    //     //     return;
    //     // }
    //     // try {
    //     //     $pdfService = new PdfService();
    //     //     // 予約タグ用のビュー (例: 'pdf_templates/reservation_tag') とデータが必要です。
    //     //     // このメソッドは PdfService.php 内の generateReservationTagPdf を呼び出す想定
    //     //     return $pdfService->generateReservationTagPdf($reservation, 'I');
    //     // } catch (\Exception $e) {
    //     //     // ... エラー処理 ...
    //     // }
    //     echo "この機能は ReservationModel とデータの準備後にテスト可能です。";
    //     echo "<hr><a href='" . site_url('dev-test') . "'>テスト一覧に戻る</a>";
    // }


    /**
     * Flatpickr Helper (app_form_helper.php) のテスト
     */
    public function testFlatpickrForm()
    {
        $data = [
            'page_title' => 'Flatpickr 機能テスト', // ★★★ レイアウトファイルで使われる変数名に合わせる ★★★
        ];

        return view('Dev/test_flatpickr', $data);      
    }
}