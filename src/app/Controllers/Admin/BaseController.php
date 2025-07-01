<?php // app/Controllers/Admin/BaseController.php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * 管理画面コントローラの基底クラス
 * このクラスは、管理者エリアの全てのコントローラに共通の機能を提供します。
 */
abstract class BaseController extends Controller
{
    /**
     * @var CLIRequest|IncomingRequest リクエストオブジェクト
     */
    protected $request;

    /**
     * ビューに渡す共通データを格納する配列。
     * initController() などで設定することを想定しています。
     * 例: $this->viewData['siteName'] = 'サイト名';
     * @var array<string, mixed>
     */
    protected array $viewData = [];

    /**
     * このコントローラおよび継承先コントローラで自動的にロードされるヘルパーのリスト。
     * アプリケーション全体で頻繁に使用するヘルパーは、
     * `app/Config/Autoload.php` の `$helpers` 配列で設定することを推奨します。
     * ここでは、このベースコントローラおよびその子クラス群に特有のヘルパーを指定する場合に使用します。
     * @var array<int, string>
     */
    protected $helpers = []; // 今回は Autoload.php に委ねるため空

    /**
     * コンストラクタ。
     * サービスの依存性注入（DI）を行います。
     *
     * @param RequestInterface  $request  現在のHTTPリクエストオブジェクト
     * @param ResponseInterface $response 現在のHTTPレスポンスオブジェクト
     * @param LoggerInterface   $logger   ロガーインスタンス
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        // 親クラスの initController を必ず呼び出すことが推奨されています。
        parent::initController($request, $response, $logger);

        // ここに、このBaseControllerを継承する全てのコントローラで
        // 実行したい共通の初期化処理を記述します。
        // 例えば、セッションの開始や、共通で使うライブラリのロードなどです。
        // $this->session = \Config\Services::session();
        // $this->viewData['currentUser'] = auth()->user(); // ログインユーザー情報を全ビューに渡すなど
    }

    /**
     * 指定されたビューを共通レイアウトを使用してレンダリングします。
     * ビューファイル内での `$this->extend()` および `$this->section()` の使用を前提としています。
     *
     * @param string $viewName ビューファイルへのパス (`app/Views/` からの相対パス、例: 'Admin/Users/index')
     * @param array<string, mixed>  $data     ビューに渡す追加データ。このメソッド内で共通の `$this->viewData` とマージされます。
     * @return string          レンダリングされたHTML文字列。
     */
    protected function render(string $viewName, array $data = []): string
    {
        // 引数で渡された $data と、クラスプロパティの $this->viewData をマージします。
        // $data で同じキーが指定された場合、$data の値が優先されます。
        $renderData = array_merge($this->viewData, $data);

        // page_title が $renderData にセットされていなければ、デフォルト値を設定します。
        if (!isset($renderData['page_title'])) {
            $renderData['page_title'] = '管理画面'; // デフォルトのページタイトル
        }

        // h1_title が明示的に設定されていない場合、page_title から自動生成
        if (!isset($renderData['h1_title'])) {
            $parts = explode(' | ', $renderData['page_title']);
            $renderData['h1_title'] = $parts[0];
        }

        // view() ヘルパー関数に、ビュー名と最終的なデータを渡してレンダリングを実行します。
        // レイアウトの適用は、$viewName で指定されたビューファイル内の $this->extend() に任せます。
        return view($viewName, $renderData);
    }

    /**
     * ユーザー個人設定を取得するAPIメソッド
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getUserPreferences()
    {
        helper('user_preference');
        
        // ログインチェック
        if (!auth()->loggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ログインが必要です。'
            ])->setStatusCode(401);
        }

        try {
            // 店舗一覧を取得
            $shopModel = model('App\Models\ShopModel');
            $shops = $shopModel->where('active', 1)
                              ->orderBy('sort_order', 'ASC')
                              ->findAll();
            
            $shopOptions = ['' => '-- 店舗を選択してください --'];
            foreach ($shops as $shop) {
                $shopOptions[$shop->id] = $shop->name;
            }

            // 現在の設定値を取得
            $preferences = [
                'default_shop_id' => user_preference('default_shop_id'),
                'pagination_per_page' => user_preference('pagination_per_page', 20),
            ];

            // オプション情報
            $options = [
                'shops' => $shopOptions,
                'pagination_options' => [
                    10 => '10件',
                    20 => '20件', 
                    50 => '50件',
                    100 => '100件',
                ],
            ];

            return $this->response->setJSON([
                'success' => true,
                'preferences' => $preferences,
                'options' => $options
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[BaseController::getUserPreferences] Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => '設定の取得に失敗しました。'
            ])->setStatusCode(500);
        }
    }

    /**
     * ユーザー個人設定を保存するAPIメソッド
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function saveUserPreferences()
    {
        helper('user_preference');
        
        // ログインチェック
        if (!auth()->loggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ログインが必要です。'
            ])->setStatusCode(401);
        }

        // POST以外は拒否
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => '不正なリクエストです。'
            ])->setStatusCode(405);
        }

        try {
            // バリデーション
            $validationRules = [
                'default_shop_id' => 'permit_empty|integer|greater_than[0]',
                'pagination_per_page' => 'required|integer|in_list[10,20,50,100]',
            ];

            $validationMessages = [
                'default_shop_id' => [
                    'integer' => '有効な店舗を選択してください。',
                    'greater_than' => '有効な店舗を選択してください。',
                ],
                'pagination_per_page' => [
                    'required' => 'ページネーション件数を選択してください。',
                    'integer' => '有効なページネーション件数を選択してください。',
                    'in_list' => '有効なページネーション件数を選択してください。',
                ],
            ];

            if (!$this->validate($validationRules, $validationMessages)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => '入力内容に問題があります。',
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(400);
            }

            // 設定値を保存
            $defaultShopId = $this->request->getPost('default_shop_id');
            $paginationPerPage = $this->request->getPost('pagination_per_page');

            $success = true;

            // デフォルト店舗ID保存（空の場合はnull）
            if ($defaultShopId === '' || $defaultShopId === null) {
                $success = $success && set_user_preference('default_shop_id', null);
            } else {
                $success = $success && set_user_preference('default_shop_id', (int)$defaultShopId);
            }

            // ページネーション件数保存
            $success = $success && set_user_preference('pagination_per_page', (int)$paginationPerPage);

            if ($success) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => '設定を保存しました。'
                ]);
            } else {
                throw new \RuntimeException('設定の保存に失敗しました。');
            }

        } catch (\Throwable $e) {
            log_message('error', '[BaseController::saveUserPreferences] Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => '設定の保存に失敗しました。再度お試しください。'
            ])->setStatusCode(500);
        }
    }
}