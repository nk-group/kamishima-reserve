<?php

namespace App\Controllers\Customer;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * 顧客向けページコントローラの基底クラス
 * このクラスは、顧客向けエリアの全てのコントローラに共通の機能を提供します。
 * 認証不要、iframe埋め込み対応はcustomer_layout.phpで実装済み。
 */
abstract class BaseController extends Controller
{
    /**
     * ビューに渡す共通データを格納する配列。
     * @var array<string, mixed>
     */
    protected array $viewData = [];

    /**
     * コントローラ初期化処理
     *
     * @param RequestInterface  $request  現在のHTTPリクエストオブジェクト
     * @param ResponseInterface $response 現在のHTTPレスポンスオブジェクト
     * @param LoggerInterface   $logger   ロガーインスタンス
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        // 親クラスの initController を必ず呼び出す
        parent::initController($request, $response, $logger);
        
        // 最小限の共通データのみ設定
        $this->viewData = [
            'site_name' => 'Clear車検予約システム',
            'company_name' => '上嶋自動車',
            'current_year' => date('Y'),
        ];
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
        $renderData = array_merge($this->viewData, $data);
        
        if (!isset($renderData['page_title'])) {
            $renderData['page_title'] = 'Clear車検予約 | 上嶋自動車';
        }
        
        if (!isset($renderData['body_id'])) {
            log_message('warning', "body_id not set for view: {$viewName}");
            $renderData['body_id'] = 'page-customer-default';
        }

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
        return $this->response->setJSON($data)->setStatusCode($statusCode);
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

        return $this->render('Customer/error/error', $data);
    }
}