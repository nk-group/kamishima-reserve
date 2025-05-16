<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index'); // CodeIgniterのデフォルトWelcomeページはそのまま残す場合

// Shieldが提供する認証ルート (ログイン、ログアウト、登録など)
// これを呼び出すことで、ShieldのAuthControllerが処理を担当します。
// Auth.phpの$viewsで指定したビューが使われます。
service('auth')->routes($routes);

// 管理者/スタッフ向けルート
// 'sessionauth' フィルタで認証を要求
// 'group:admin,staff' フィルタで admin または staff グループに所属しているかチェック
$routes->group('admin', ['filter' => 'sessionauth', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
  /** @var RouteCollection $routes */
  $routes->get('', 'DashboardController::index', ['as' => 'admin.home']);
  $routes->get('dashboard', 'DashboardController::index', ['as' => 'admin.dashboard']);


  // --- ユーザー管理ルート ---
  // 'group:admin' フィルターを追加して、adminグループのユーザーのみアクセスを許可
  $routes->group('users', ['filter' => 'group:admin'], static function ($routes) {
      /** @var RouteCollection $routes */
      $routes->get('', 'UserController::index', ['as' => 'admin.users.index']); // ユーザー一覧表示 (GET /admin/users)
      $routes->get('new', 'UserController::new', ['as' => 'admin.users.new']);         // 新規作成フォーム表示 (GET /admin/users/new) (後で追加)
      $routes->post('create', 'UserController::create', ['as' => 'admin.users.create']); // 新規作成処理 (POST /admin/users/create) (後で追加)
      $routes->get('edit/(:num)', 'UserController::edit/$1', ['as' => 'admin.users.edit']); // 編集フォーム表示 (GET /admin/users/edit/1) (後で追加)
      $routes->post('update/(:num)', 'UserController::update/$1', ['as' => 'admin.users.update']); // 更新処理 (POST /admin/users/update/1) (後で追加)
      $routes->get('delete/(:num)', 'UserController::delete/$1', ['as' => 'admin.users.delete']); // 削除処理 (GET /admin/users/delete/1) (後で追加、POST推奨)
  });


});



// ルートの優先順位を考慮して、Shieldのルート定義の後にカスタムルートを定義するか、
// Shieldのルートで不要なものをコメントアウト/上書きすることも可能です。
// 今回は service('auth')->routes($routes); を利用し、
// /login, /logout などはShieldのコントローラに任せます。

// Filters.php で 'admin/*' に 'sessionauth' をかけていますが、
// Shieldのデフォルトのログインページは /login なので、
// /admin/login のようなパスにしない限りはフィルタの影響を受けません。
// もし /admin/login のようなパスにしたい場合は、Filters.phpの適用除外設定が必要です。
// 今回は /login を使うため、そのままで問題ありません。


// --- 利用者向けページ ---
$routes->get('/calendar', 'User\CalendarController::index', ['as' => 'user.calendar']);

// 他の利用者向けルートもここに追加していく
// $routes->get('/', 'User\HomeController::index', ['as' => 'user.home']); // もしトップページがあれば



// --- 開発テスト用のページ ---
if (ENVIRONMENT === 'development') {
    $routes->group('dev-test', ['namespace' => 'App\Controllers\Dev'], static function ($routes) {
        $routes->get('/', 'TestController::index');
        $routes->get('auth', 'TestController::testAuthHelper');
        $routes->get('pdf-sample', 'TestController::testPdfService');
        $routes->get('flatpickr-form', 'TestController::testFlatpickrForm');
        $routes->get('lab', 'TestController::lab');
        // $routes->post('lab', 'TestController::lab'); // もしフォームで何か送信してテストする場合はPOSTも許可
    });
}