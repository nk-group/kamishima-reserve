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
}