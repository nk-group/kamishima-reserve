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


    // --- 車両種別マスタ管理ルート ---
    // 'group:admin' フィルターを追加して、adminグループのユーザーのみアクセスを許可
    $routes->group('vehicle-types', ['filter' => 'group:admin'], static function ($routes) {
        /** @var RouteCollection $routes */
        $routes->get('', 'VehicleTypeController::index', ['as' => 'admin.vehicle-types.index']);    // 一覧表示
        $routes->get('new', 'VehicleTypeController::new', ['as' => 'admin.vehicle-types.new']);     // 新規作成フォーム
        $routes->post('create', 'VehicleTypeController::create', ['as' => 'admin.vehicle-types.create']); // 新規作成処理
        $routes->get('edit/(:num)', 'VehicleTypeController::edit/$1', ['as' => 'admin.vehicle-types.edit']); // 編集フォーム
        $routes->post('update/(:num)', 'VehicleTypeController::update/$1', ['as' => 'admin.vehicle-types.update']); // 更新処理
        $routes->post('delete/(:num)', 'VehicleTypeController::delete/$1', ['as' => 'admin.vehicle-types.delete']); // 削除処理
    });

    // --- 予約管理ルート ---
    // URL: /admin/reservations/...
    // Filter: 'staff.access' パーミッションを持つユーザーがアクセス可能
    // Controller: App\Controllers\Admin\ReservationController
    $routes->group('reservations', ['filter' => 'permission:staff.access'], static function ($routes) { // ★ フィルターを 'permission:staff.access' に変更
        /** @var RouteCollection $routes */
        
        $routes->get('new', 'ReservationController::new', ['as' => 'admin.reservations.new']);
        $routes->post('create', 'ReservationController::create', ['as' => 'admin.reservations.create']);
        // 以下、必要に応じて一覧、編集、更新、削除などのRESTfulなルートも同様の形式で定義可能です。
        // $routes->get('', 'Reservations::index', ['as' => 'admin.reservations.index']); // 予約一覧
        // $routes->get('show/(:num)', 'Reservations::show/$1', ['as' => 'admin.reservations.show']); // 予約詳細 (もしあれば)
        // $routes->get('edit/(:num)', 'Reservations::edit/$1', ['as' => 'admin.reservations.edit']); // 予約編集フォーム
        // $routes->post('update/(:num)', 'Reservations::update/$1', ['as' => 'admin.reservations.update']); // 予約更新処理
        // $routes->post('delete/(:num)', 'Reservations::delete/$1', ['as' => 'admin.reservations.delete']); // 予約削除処理
    });    

});



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