<?php

namespace App\Controllers\Customer;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * 顧客向けページコントローラの基底クラス
 * このクラスは、顧客向けエリアの全てのコントローラに共通の機能を提供します。
 * 認証不要、iframe埋め込み対応、CSS競合回避などの機能を含みます。
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
     * @var array<string, mixed>
     */
    protected array $viewData = [];

    /**
     * このコントローラおよび継承先コントローラで自動的にロードされるヘルパーのリスト。
     * @var array<int, string>
     */
    protected $helpers = [];

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
        // 親クラスの initController を必ず呼び出す
        parent::initController($request, $response, $logger);

        // 顧客向けページ共通の初期化処理
        $this->setupCustomerDefaults();
        
        // iframe対応のレスポンスヘッダー設定
        $this->setupIframeHeaders();
    }

    /**
     * 顧客向けページのデフォルト設定
     */
    private function setupCustomerDefaults(): void
    {
        // 顧客向けページ共通のデータを設定
        $this->viewData = [
            'site_name' => 'Clear車検予約システム',
            'company_name' => '上嶋自動車',
            'current_year' => date('Y'),
            'is_iframe' => $this->isIframeRequest(),
        ];
    }

    /**
     * iframe埋め込み対応のレスポンスヘッダー設定
     */
    private function setupIframeHeaders(): void
    {
        // iframe埋め込み許可のためのヘッダー設定
        $this->response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $this->response->setHeader('Content-Security-Policy', "frame-ancestors 'self' *.kamishima.co.jp");
        
        // CORS設定（必要に応じて）
        if ($this->isIframeRequest()) {
            $this->response->setHeader('Access-Control-Allow-Origin', '*');
            $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
        }
    }

    /**
     * iframe内からのリクエストかどうかを判定
     *
     * @return bool
     */
    private function isIframeRequest(): bool
    {
        // リファラーやHTTPヘッダーから判定
        $referer = $this->request->getServer('HTTP_REFERER');
        $userAgent = $this->request->getServer('HTTP_USER_AGENT');
        
        // iframe判定のロジック（必要に応じて調整）
        return !empty($referer) && strpos($referer, 'kamishima.co.jp') !== false;
    }

    /**
     * 指定されたビューを顧客向けレイアウトを使用してレンダリングします。
     * 
     * @param string $viewName ビューファイルへのパス (app/Views/ からの相対パス)
     * @param array<string, mixed> $data ビューに渡す追加データ
     * @return string レンダリングされたHTML文字列
     */
    protected function render(string $viewName, array $data = []): string
    {
        // 引数で渡された $data と、クラスプロパティの $this->viewData をマージ
        $renderData = array_merge($this->viewData, $data);

        // page_title が設定されていなければ、デフォルト値を設定
        if (!isset($renderData['page_title'])) {
            $renderData['page_title'] = 'Clear車検予約 | 上嶋自動車';
        }

        // h1_title が明示的に設定されていない場合、page_title から自動生成
        if (!isset($renderData['h1_title'])) {
            $parts = explode(' | ', $renderData['page_title']);
            $renderData['h1_title'] = $parts[0];
        }

        // body_id が設定されていない場合は警告ログ出力
        if (!isset($renderData['body_id'])) {
            log_message('warning', "body_id not set for view: {$viewName}");
            $renderData['body_id'] = 'page-customer-default';
        }

        // レスポンシブ・iframe対応のmeta設定
        $renderData['meta_viewport'] = 'width=device-width, initial-scale=1';
        $renderData['meta_robots'] = 'noindex, nofollow'; // 顧客向けページは検索エンジンにインデックスさせない

        return view($viewName, $renderData);
    }

    /**
     * JSONレスポンスを返すヘルパーメソッド
     * 
     * @param array<string, mixed> $data レスポンスデータ
     * @param int $statusCode HTTPステータスコード
     * @return ResponseInterface
     */
    protected function jsonResponse(array $data, int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setJSON($data)
            ->setStatusCode($statusCode);
    }

    /**
     * エラーページを表示
     * 
     * @param string $message エラーメッセージ
     * @param int $statusCode HTTPステータスコード
     * @return string
     */
    protected function showError(string $message, int $statusCode = 404): string
    {
        $this->response->setStatusCode($statusCode);
        
        $data = [
            'page_title' => 'エラー | Clear車検予約',
            'body_id' => 'page-customer-error',
            'error_message' => $message,
            'status_code' => $statusCode,
        ];

        return $this->render('Customer/error', $data);
    }

    /**
     * リダイレクト処理（iframe対応）
     * 
     * @param string $uri リダイレクト先URI
     * @param string $message フラッシュメッセージ
     * @param string $type メッセージタイプ (success, error, warning, info)
     * @return ResponseInterface
     */
    protected function redirectWithMessage(string $uri, string $message = '', string $type = 'info'): ResponseInterface
    {
        if (!empty($message)) {
            session()->setFlashdata($type, $message);
        }

        // iframe内の場合は特別な処理が必要な場合があります
        if ($this->isIframeRequest()) {
            // 必要に応じてJavaScript redirectを使用
            // 現在はそのままリダイレクト
        }

        return redirect()->to($uri);
    }

    /**
     * フォームバリデーションエラーの統一処理
     * 
     * @param array<string, string> $errors バリデーションエラー配列
     * @return array<string, mixed> ビューに渡すエラーデータ
     */
    protected function formatValidationErrors(array $errors): array
    {
        return [
            'has_errors' => !empty($errors),
            'errors' => $errors,
            'error_count' => count($errors),
        ];
    }
}